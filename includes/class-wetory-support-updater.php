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
     * @var type 
     */
    private $slug;

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

        //wetory_write_log("Register new updater for " . $github_repo, 'info');
    }

    /**
     * Get information regarding our plugin from WordPress
     * 
     * @since      1.0.1
     */
    private function init_plugin_data() {

        //wetory_write_log("Initializing updater plugin data for " . $this->repo, 'info');

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

        // wetory_write_log("Going to download plugin " . $this->repo . " releases from " . $url, 'info');
        // We need the access token for private repos
        if (!empty($this->access_token)) {
            $response = wp_remote_get($url, array('headers' => array('Authorization' => 'token ' . $this->access_token)));
        } else {
            $response = wp_remote_get($url);
        }

        // Get the results
        $this->github_API_result = wp_remote_retrieve_body($response);
        if (!empty($this->github_API_result)) {

            // wetory_write_log("Plugin releases downloaded for " . $this->repo, 'info');

            $this->github_API_result = @json_decode($this->github_API_result);
        }

        // Use only the latest release if $all set to false
        if (is_array($this->github_API_result) && !$all) {
            $this->github_API_result = $this->github_API_result[0];
        }

        // Uncomment this to get detailed JSON with all data retrievede from GitHub API
        //wetory_write_log($this->github_API_result, 'info');
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

        // wetory_write_log("Latest release version - " . $this->repo . ": " . $this->github_API_result->tag_name, 'info');
        // wetory_write_log("Installed version - " . $this->repo . ": " . $transient->checked[$this->slug], 'info');
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

        wetory_write_log($this->github_API_result, 'info');

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
        switch ($this->update_version) {
            case '1.1.0':
                delete_option('wetory-support-admin_notice_message');
                delete_option('wetory-support-libraries');
                break;

            default:
                break;
        }
    }

}
