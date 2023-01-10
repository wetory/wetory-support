<?php

/**
 * Class managing plugin updates
 *
 * This plugin is not published to public WordPress repository so requires some
 * mechanism for updates using some private repository. It is hosted on GitHub
 * so have to handle updates from there.
 * 
 * https://code.tutsplus.com/tutorials/distributing-your-plugins-in-github-with-automatic-updates--wp-34817
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.1
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
/**
 * Load class for parsing GitHub markdown - https://github.com/erusev/parsedown
 */
require_once(WETORY_SUPPORT_PATH . 'includes/class-parsedown.php' );

class Wetory_Support_Updater {

    /**
     * Plugin slug
     * @var string 
     */
    private string $slug;

    /**
     * Plugin data
     * @var type 
     */
    private $plugin_data;

    /**
     * GitHub username
     * @var type 
     */
    private $username;

    /**
     * GitHub repository
     * @var type 
     */
    private $repo;
    
    /**
     * Holds info about incoming update version
     * @var string
     */
    private $update_version;

    /**
     * __FILE__ of plugin
     * @var type 
     */
    private $plugin_file;

    /**
     * Holds data from GitHub
     * @var type 
     */
    private $github_API_result;

    /**
     * GitHub private repo token
     * @var type 
     */
    private $access_token;

    /**
     * Flag if plugin is activated
     * @var type 
     */
    private $plugin_activated;

    /**
     * Create new instance
     * 
     * @param string $plugin_file This should have the value __FILE__. Will be getting details about plugin from this later on.
     * @param string $github_username GitHub username
     * @param string $github_repo GitHub repository
     * @param string $access_token An access token that will allow us to view the details of a private GitHub repo. If your project is hosted in a public GitHub repo, just leave this blank.
     */
    function __construct($plugin_file, $github_username, $github_repo, $access_token = '') {
        add_filter("pre_set_site_transient_update_plugins", array($this, "set_transitent"));
        add_filter("plugins_api", array($this, "set_plugin_info"), 10, 3);
        add_filter("upgrader_pre_install", array($this, "pre_install"), 10, 3);
        add_filter("upgrader_post_install", array($this, "post_install"), 10, 3);

        $this->plugin_file = $plugin_file;
        $this->username = $github_username;
        $this->repo = $github_repo;
        $this->access_token = $access_token;

        wetory_write_log(sprintf(__('Custom updater registered for GitHub repository %s','wetory-support'), $github_repo));
    }

    /**
     * Get information regarding our plugin from WordPress
     * 
     * @since      1.0.1
     */
    private function init_plugin_data() {

        wetory_write_log(sprintf(__('Initializing custom updater data for GitHub repository %s','wetory-support'), $this->repo));

        $this->slug = plugin_basename($this->plugin_file);
        $this->plugin_data = get_plugin_data($this->plugin_file);
    }

    /**
     * Get information regarding our plugin from GitHub
     * 
     * @since      1.0.1
     * 
     * @param boolean $all If set to true all releases are loaded instead of latest one
     */
    private function get_repo_release_info($all = false) {
        if (!empty($this->github_API_result)) {
            return;
        }

        // Query the GitHub API. 
        $url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";

        wetory_write_log(sprintf(__('Going to download plugin %s releases from %s','wetory-support'), $this->repo, $url));
        
        // We need the access token for private repos
        if (!empty($this->access_token)) {
            $response = wp_remote_get($url, array('headers' => array('Authorization' => 'token ' . $this->access_token)));
        } else {
            $response = wp_remote_get($url);
        }

        // Get the results
        $this->github_API_result = wp_remote_retrieve_body($response);
        if (!empty($this->github_API_result)) {

            wetory_write_log(sprintf(__('Plugin releases info downloaded for %s','wetory-support'), $this->repo));

            $this->github_API_result = @json_decode($this->github_API_result);
        }

        // Use only the latest release if $all set to false
        if (is_array($this->github_API_result) && !$all) {
            $this->github_API_result = $this->github_API_result[0];
        }

        wetory_write_log($this->github_API_result);
    }

    /**
     * Push in plugin version information to get the update notification
     * 
     * @since      1.0.1
     * 
     * @param type $transient
     * @return type
     */
    public function set_transitent($transient) {
        // If we have checked the plugin data before, don't re-check
        if (empty($transient->checked)) {
            return $transient;
        }

        // Get plugin & GitHub release information
        $this->init_plugin_data();
        $this->get_repo_release_info();

        wetory_write_log(sprintf(__('Latest release version - %s : %s','wetory-support'), $this->repo, $this->github_API_result->tag_name));
        wetory_write_log(sprintf(__('Installed version - %s : %s','wetory-support'), $this->repo, $transient->checked[$this->slug]));

        // Check the versions if we need to do an update
        $do_update = version_compare($this->github_API_result->tag_name, $transient->checked[$this->slug]);

        // Update the transient to include our updated plugin data
        if ($do_update == 1) {
            $package = $this->github_API_result->zipball_url;

            // Include the access token for private GitHub repos
            if (!empty($this->access_token)) {
                $package = add_query_arg(array("access_token" => $this->access_token), $package);
            }

            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $this->github_API_result->tag_name;
            $obj->url = $this->plugin_data["PluginURI"];
            $obj->package = $package;
            $transient->response[$this->slug] = $obj;
        }

        return $transient;
    }

    /**
     * Push in plugin version information to display in the details lightbox
     * 
     * @since      1.0.1
     * 
     * @param type $false
     * @param type $action
     * @param type $response
     * @return type
     */
    public function set_plugin_info($false, $action, $response) {
        // Get plugin & GitHub release information
        $this->init_plugin_data();
        $this->get_repo_release_info($all = true);

        wetory_write_log($this->github_API_result);

        // If nothing is found, do nothing
        if (empty($response->slug) || $response->slug != $this->slug) {
            return $false;
        }

        // Add our plugin information
        $response->last_updated = $this->github_API_result[0]->published_at;
        $response->slug = $this->slug;
        $response->name = $this->plugin_data["Name"];
        $response->version = $this->update_version = $this->github_API_result[0]->tag_name;
        $response->author = $this->plugin_data["AuthorName"];
        $response->homepage = $this->plugin_data["PluginURI"];

        // This is our release download zip file
        $download_link = $this->github_API_result[0]->zipball_url;

        // Include the access token for private GitHub repos
        if (!empty($this->access_token)) {
            $download_link = add_query_arg(
                    array("access_token" => $this->access_token),
                    $download_link
            );
        }
        $response->download_link = $download_link;

        // Create tabs in the lightbox
        $response->sections = array(
            'description' => $this->plugin_data["Description"],
            'changelog' => $this->parse_changelog(),
        );

        // Gets the required version of WP if available
        $matches = null;
        preg_match("/requires:\s([\d\.]+)/i", $this->github_API_result[0]->body, $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->requires = $matches[1];
                }
            }
        }

        // Gets the tested version of WP if available
        $matches = null;
        preg_match("/tested:\s([\d\.]+)/i", $this->github_API_result[0]->body, $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->tested = $matches[1];
                }
            }
        }

        return $response;
    }

    private function parse_changelog() {
        $changelog = '';
        if (is_array($this->github_API_result)) {
            foreach ($this->github_API_result as $release) {
                $changelog .= $this->get_release_info($release);
            }
        } else {
            $release = $this->github_API_result;
            $changelog .= $this->get_release_info($release);
        }
        return $changelog;
    }
    
    private function get_release_info($release) {
        $release_info = '';
        $release_info .= '<h4>' . $release->tag_name . '</h4>';
        $release_info .= class_exists("Parsedown") ? Parsedown::instance()->parse($release->body) : $release->body;
        return $release_info;
    }

    /**
     * Perform check before installation starts.
     *
     * @since      1.0.3
     * 
     * @param  boolean $true
     * @param  array   $args
     * @return null
     */
    public function pre_install($true, $args) {
        // Get plugin information
        $this->init_plugin_data();

        // Check if the plugin was installed before...
        $this->plugin_activated = is_plugin_active($this->slug);
    }

    /**
     * Perform additional actions to successfully install our plugin
     * 
     * @since      1.0.1
     * 
     * @param type $true
     * @param type $hook_extra
     * @param type $result
     * @return type
     */
    public function post_install($true, $hook_extra, $result) {
        // Get plugin information
        $this->init_plugin_data();

        /**
         * Since we are hosted in GitHub, our plugin folder would have a dirname of
         * reponame-tagname change it to our original one:
         */
        global $wp_filesystem;
        $plugin_folder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->slug);
        $wp_filesystem->move($result['destination'], $plugin_folder);
        $result['destination'] = $plugin_folder;

        // Re-activate plugin if needed
        if ($this->plugin_activated) {
            $activate = activate_plugin($this->slug);
        }
        
        // Do some post install tasks
        $this->post_install_tasks();

        return $result;
    }
    
    /**
     * This function contains some actions that can be done after update.
     * 
     * For example when there is some stuff in database that is not needed anymore
     * it can be cleaned in this function. Just add case condition to switch statement
     * and specify what to do.
     * 
     * @since 1.1.0
     */
    public function post_install_tasks(){
        wetory_write_log(sprintf(__('Running post-update actions for version %s','wetory-support'), $this->update_version));
        $update_version_int = (int)str_replace('.', '', $this->update_version);
        $current_version_int = (int)str_replace('.', '', WETORY_SUPPORT_VERSION);
        switch (true) {
            case ($update_version_int == 110):
                delete_option('wetory-support-admin_notice_message');
                delete_option('wetory-support-libraries');
                break;
            case ($current_version_int < 121 && $update_version_int >= 121):
                $this->migrate_options_121();
                break;
            default:
                break;
        }
    }

    /**
     * Special function for migrating options when doing plugin 
     * update to version greater than 1.2.0
     * 
     * This is important to keep options that were stored for website
     * before update as way of storing settings has been changed in
     * plugin version 1.2.1
     * 
     * @since 1.2.1
     *
     * @return void
     */
    public function migrate_options_121(){
        // Get "old way" options data
        $option_apikeys = get_option('wetory-support-apikeys');
        $option_cpt = get_option('wetory-support-cpt');
        $option_shortcodes = get_option('wetory-support-shortcodes');
        $option_widgets = get_option('wetory-support-widgets');

        // Get "new way" options data
        $plugin_settings = Wetory_Support_Options::get_settings_option();

        // Apply "old way" to "new way"
        if (isset($option_apikeys) && !empty($option_apikeys)) {
            $plugin_settings[WETORY_SUPPORT_SETTINGS_APIKEYS_SECTION] = $option_apikeys;
        }
        if (isset($option_cpt) && !empty($option_cpt)) {
            $plugin_settings[WETORY_SUPPORT_SETTINGS_CPT_SECTION] = $option_cpt;
        }
        if (isset($option_shortcodes) && !empty($option_shortcodes)) {
            $plugin_settings[WETORY_SUPPORT_SETTINGS_SHORTCODES_SECTION] = $option_shortcodes;
        }
        if (isset($option_widgets) && !empty($option_widgets)) {
            $plugin_settings[WETORY_SUPPORT_SETTINGS_WIDGETS_SECTION] = $option_widgets;
        }     

        // Sanitise & validate "new way" options data
        $plugin_settings = apply_filters('wetory_settings_sanitize', $plugin_settings);
        $plugin_settings = apply_filters('wetory_settings_validate', $plugin_settings);
        if (is_wp_error($plugin_settings)) {            
            wp_die($plugin_settings);
        }

        // Update "new way" options data in database
        update_option(WETORY_SUPPORT_SETTINGS_OPTION, $plugin_settings);

        // Delete "old way" options data from database
        delete_option('wetory-support-apikeys');
        delete_option('wetory-support-cpt');
        delete_option('wetory-support-shortcodes');
        delete_option('wetory-support-widgets');
        delete_option('wetory-support-general');

        Wetory_Support_Admin_Notices::info(__('Wetory Support - Plugin settings options migrated for version 1.2.1', 'wetory-support'), true);
    }

}
