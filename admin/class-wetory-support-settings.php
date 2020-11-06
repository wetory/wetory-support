<?php

/**
 * Define settings available for the plugin
 *
 * Adding menu item and settings page to administration section. Should be instantiated only for admin
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Settings_Renderer as Settings_Renderer;

class Wetory_Support_Settings {

    /**
     * Specify some constants to make it easy with changing values
     */
    const GENERAL_OPTION = 'wetory-support-general';
    const WIDGETS_OPTION = 'wetory-support-widgets';
    const SHORTCODES_OPTION = 'wetory-support-shortcodes';
    const LIBRARIES_OPTION = 'wetory-support-libraries';
    const APIKEYS_OPTION = 'wetory-support-apikeys';
    const CPTS_OPTION = 'wetory-support-cpts';

    /**
     * Libraries controller that is responsible for all external libraries offered in this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Libraries_Controller  $plugin_libraries    Maintains and enqueue all libraries from the plugin.
     */
    private $plugin_libraries;

    /**
     * Widgets controller that is responsible for all Widget objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Widgets_Controller  $plugin_widgets    Maintains and registers all widgets from the plugin.
     */
    private $plugin_widgets;

    /**
     * Shortcodes controller that is responsible for all Shortcode objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Shortcodes_Controller  $plugin_shortcodes    Maintains and registers all shortcodes from the plugin.
     */
    private $plugin_shortcodes;

    /**
     * API keys controller that is responsible for all API key objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Apikeys_Controller  $plugin_apikeys  Maintains all API keys from the plugin.
     */
    private $plugin_apikeys;

    /**
     * Custom post types controller that is responsible for all custom post type objects from this plugin.
     *
     * @since    1.1.0
     * @access   protected
     * @var      Wetory_Support_Cpt_Controller  $plugin_cpts  Maintains all custom post type from the plugin.
     */
    private $plugin_cpts;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Associative array holding plugin pages links
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $links Associative array.
     */
    private $links;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      Wetory_Support_Libraries_Controller      $plugin_libraries   The reference to the class that manages the libraries in the plugin.
     * @param      Wetory_Support_Widgets_Controller        $plugin_widgets     The reference to the class that manages the widgets in the plugin.
     * @param      Wetory_Support_Shortcodes_Controller     $plugin_shortcodes  The reference to the class that manages the shortcodes in the plugin.
     * @param      Wetory_Support_Apikeys_Controller        $plugin_apikeys     The reference to the class that manages the API keys in the plugin.
     * @param      Wetory_Support_Cpt_Controller            $plugin_cpts        The reference to the class that manages the custom post types in the plugin.
     * @param      string                                   $plugin_name        The name of this plugin.
     */
    public function __construct($plugin_libraries, $plugin_widgets, $plugin_shortcodes, $plugin_apikeys, $plugin_cpts, $plugin_name) {

        $this->plugin_libraries = $plugin_libraries;
        $this->plugin_widgets = $plugin_widgets;
        $this->plugin_shortcodes = $plugin_shortcodes;
        $this->plugin_apikeys = $plugin_apikeys;
        $this->plugin_cpts = $plugin_cpts;
        $this->plugin_name = $plugin_name;

        $this->build_links();
        $this->register_option_actions();

        $this->options = array();
    }

    /**
     * Helper function for build links used among pages in this plugin.
     * 
     * Links array private member can be easily used in page templates.
     * 
     * @since    1.0.0
     */
    private function build_links() {
        $this->links = array();
        $this->links['dashboard'] = array(
            'slug' => $this->plugin_name,
            'url' => esc_url(add_query_arg(
                            'page',
                            $this->plugin_name,
                            get_admin_url() . 'admin.php'
            ))
        );
        $this->links['settings'] = array(
            'slug' => $this->plugin_name . '-settings',
            'url' => esc_url(add_query_arg(
                            'page',
                            $this->plugin_name . '-settings',
                            get_admin_url() . 'admin.php'
            ))
        );
    }

    /**
     * Fire actions on option update
     * 
     * Some actions can be fired only when options are updated instead of calling 
     * all the time when working with website
     */
    private function register_option_actions() {
        // Code here
    }

    /**
     * Add options page to admin are
     * 
     * @since    1.0.0
     */
    public function add_settings_pages() {
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(
                __('Dashboard - Wetory', 'wetory-support'),
                __('Wetory', 'wetory-support'),
                'administrator',
                $this->links['dashboard']['slug'],
                array($this, 'display_plugin_admin_dashboard_cb'),
                'https://drive.google.com/uc?export=view&id=1GoUty6SZcPeZwg74-aC8f4A5Iz5zxb0x',
                999
        );

        //add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        add_submenu_page(
                $this->plugin_name,
                __('Settings - Wetory', 'wetory-support'),
                __('Settings', 'wetory-support'),
                'administrator',
                $this->links['settings']['slug'],
                array($this, 'display_plugin_admin_settings_cb')
        );
    }

    /**
     * Handle admin notices from settings page
     * 
     * @param array $error_message
     * @param string $type Message type, controls HTML class. Possible values include 'error', 'success', 'warning', 'info'. Default value: 'error'
     * 
     * @since    1.0.0
     */
    public function add_settings_message($error_message, $type = 'error') {
        switch ($error_message) {
            case '1':
                $message = __('There was an error adding this setting. Please try again or contact plugin author.', 'wetory-support');
                $err_code = esc_attr('wetory_support_settings');
                $setting_field = 'wetory_support_settings';
                break;
            case 'nonce_verification_failed':
                $message = __('Nonce verification failed for this form. Please try again or contact plugin author.', 'wetory-support');
                $err_code = esc_attr('wetory_support_settings');
                $setting_field = 'wetory_support_settings';
                break;
        }
        add_settings_error($setting_field, $err_code, $message, $type);
    }

    /**
     * Register and add settings
     * 
     * @since    1.0.0
     */
    public function register_and_build_fields() {

        // add maintenance section
        $this->add_maintenance_options($this->plugin_name . '-settings-general');

        // add widgets section
        $this->add_widgets_options($this->plugin_name . '-settings-general');

        // add shortcodes section
        $this->add_shortcodes_options($this->plugin_name . '-settings-general');

        // add API keys section
        $this->add_apikeys_options($this->plugin_name . '-settings-apikeys');

        // add libraries option to private member
        array_push($this->options, self::LIBRARIES_OPTION);
    }

    /**
     * Add maintenance settings as one section and option. Just created wrapper function for that.
     * 
     * @param string $settings_page Option page defines page on which to add this section of options and also group
     * 
     * @since    1.0.0
     */
    private function add_maintenance_options($settings_page) {

        $option_name = self::GENERAL_OPTION;
        $settings_section = $settings_page . '-maintenance-section';

        // register option for widgets use
        register_setting($settings_page, $option_name);
        array_push($this->options, $option_name);

        // add widgets sections
        add_settings_section(
                $settings_section,
                __('Maintenance', 'wetory-support'),
                array($this, 'print_maintenance_section_info'),
                $settings_page
        );

//        unset($args);
//        $args = array(
//            'type' => 'checkbox',
//            'option_name' => $option_name,
//            'option_key' => 'maintenance',
//            'id' => 'maintenance-custom-page',
//            'name' => 'custom-page',
//            'link' => 'https://developer.wordpress.org/reference/functions/wp_maintenance/',
//            'help' => __('Custom maintenance.php file will be generated in wp_content folder.'),
//        );
//        add_settings_field(
//                'maintenance-custom-page',
//                __('Custom maintenance page', 'wetory-support'),
//                array(Callbacks::class, 'render_settings_field'),
//                $settings_page,
//                $settings_section,
//                $args,
//        );
    }

    /**
     * Add widgets settings as one section and option. Just created wrapper function for that.
     * 
     * @param string $settings_page Option page defines page on which to add this section of options and also group
     * 
     * @since    1.0.0
     */
    private function add_widgets_options($settings_page) {

        $option_name = self::WIDGETS_OPTION;
        $settings_section = $settings_page . '-widgets-section';

        // register option for widgets use
        register_setting($settings_page, $option_name);
        array_push($this->options, $option_name);

        // add widgets sections
        add_settings_section(
                $settings_section,
                __('Widgets', 'wetory-support'),
                array($this, 'print_widgets_section_info'),
                $settings_page
        );

        // Loop through all plugin's loaded widgets
        $widgets = $this->plugin_widgets->get_objects();

        if ($widgets) {

            foreach ($widgets as $widget) {

                $widget_id = $widget->get_id();
                $widget_meta = $widget->get_meta();

                unset($args);
                $args = array(
                    'type' => 'checkbox',
                    'option_name' => $option_name,
                    'option_key' => $widget_id,
                    'id' => $widget_id . '-use',
                    'name' => 'use',
                    'link' => $widget_meta['link'],
                    'help' => $widget_meta['description'],
                );
                add_settings_field(
                        $widget_id . '-use',
                        $widget_meta['name'],
                        array(Settings_Renderer::class, 'render_settings_field'),
                        $settings_page,
                        $settings_section,
                        $args,
                );
            }
        }
    }

    /**
     * Add shortcode settings as one section and option. Just created wrapper function for that.
     * 
     * @param string $settings_page Option page defines page on which to add this section of options and also group
     * 
     * @since    1.0.0
     */
    private function add_shortcodes_options($settings_page) {

        $option_name = self::SHORTCODES_OPTION;
        $settings_section = $settings_page . '-shortcodes-section';

        // register option for shortcodes use
        register_setting($settings_page, $option_name);
        array_push($this->options, $option_name);

        // add widgets sections
        add_settings_section(
                $settings_section,
                __('Shortcodes', 'wetory-support'),
                array($this, 'print_shortcodes_section_info'),
                $settings_page
        );

        // Loop through all plugin's loaded shortcodes
        $shortcodes = $this->plugin_shortcodes->get_objects();

        if ($shortcodes) {

            foreach ($shortcodes as $shortcode) {

                $shortcode_markup = $shortcode->get_shortcode();
                $shortcode_id = $shortcode->get_id();
                $shortcode_meta = $shortcode->get_meta();

                unset($args);
                $args = array(
                    'type' => 'checkbox',
                    'option_name' => $option_name,
                    'option_key' => $shortcode_id,
                    'id' => $shortcode_id . '-use',
                    'name' => 'use',
                    'link' => $shortcode_meta['link'],
                    'help' => $shortcode_meta['description'],
                );
                add_settings_field(
                        $shortcode_id . '-use',
                        $shortcode_meta['name'] . ' ' . $shortcode_markup,
                        array(Settings_Renderer::class, 'render_settings_field'),
                        $settings_page,
                        $settings_section,
                        $args,
                );
            }
        }
    }

    /**
     * Add API keys settings as one section and option. Just created wrapper function for that.
     * 
     * @param string $settings_page Option page defines page on which to add this section of options and also group
     * 
     * @since    1.0.0
     */
    private function add_apikeys_options($settings_page) {

        $option_name = self::APIKEYS_OPTION;

        // register option for API keys use
        register_setting($settings_page, $option_name);
        array_push($this->options, $option_name);

        // Loop through all plugin's loaded API key objects
        $apikeys = $this->plugin_apikeys->get_objects();

        if ($apikeys) {

            foreach ($apikeys as $apikey) {

                $apikey_id = $apikey->get_id();
                $apikey_meta = $apikey->get_meta();

                $settings_section = $settings_page . '-apikeys-' . $apikey_id . '-section';
                add_settings_section(
                        $settings_section,
                        __($apikey_meta['name'], 'wetory-support'),
                        '',
                        $settings_page
                );

                $keys = $apikey->get_keys();
                foreach ($keys as $key => $label) {
                    unset($args);
                    $args = array(
                        'type' => 'text',
                        'option_name' => $option_name,
                        'option_key' => $apikey_id,
                        'id' => $apikey_id . '-' . $key,
                        'name' => $key,
                        'link' => $apikey_meta['link'],
                        'help' => $apikey_meta['description'],
                    );
                    add_settings_field(
                            $apikey_id . '-' . $key,
                            $label,
                            array(Settings_Renderer::class, 'render_settings_field'),
                            $settings_page,
                            $settings_section,
                            $args,
                    );
                }
            }
        }
    }

    /**
     * Render custom settings form.
     * 
     * Function rendering custom settings form in form of table where rows are plugin libraries
     * and columns are library options. This is nicely feasible as all libraries enqueueing funcitons
     * are same. Using special render function
     * 
     * @see Wetory_Support_Settings_Renderer::render_horizontal_form_table($args, $data) 
     * 
     * @since    1.0.0
     */
    private function render_libraries_form_table() {

        $option_name = self::LIBRARIES_OPTION;

        // Loop through all plugin's loaded libraries
        $libraries = $this->plugin_libraries->get_objects();

        unset($args);
        $args = array(
            'option_name' => $option_name,
            'columns' => array(
                'title' => array(
                    'label' => __('Library', 'wetory-support'),
                    'type' => 'raw',
                ),
                'use-public' => array(
                    'label' => __('Use in frontend', 'wetory-support'),
                    'type' => 'checkbox',
                ),
                'use-admin' => array(
                    'label' => __('Use in admin', 'wetory-support'),
                    'type' => 'checkbox',
                ),
                'version' => array(
                    'label' => __('Version', 'wetory-support'),
                    'type' => 'select',
                    'options' => 'versions'
                ),
                'cdn' => array(
                    'label' => __('CDN', 'wetory-support'),
                    'type' => 'checkbox',
                ),
                'description' => array(
                    'label' => '',
                    'type' => 'tooltip',
                    'source' => 'meta',
                    'class' => 'compact',
                ),
                'link' => array(
                    'label' => '',
                    'type' => 'link',
                    'source' => 'meta',
                    'class' => 'compact',
                )
            ),
        );
        Settings_Renderer::render_horizontal_form_table($args, $libraries);
    }

    /**
     * Handle custom form submit.
     * 
     * This function is called when custom form for plugin libraries settings is submitted.
     * Simple checking of valid nonce and updating option in database.
     * 
     * @since    1.0.0
     */
    private function update_libraries_settings() {
        // Check nonce first
        if (!isset($_POST['wetory_support_settings_libraries_form']) || !wp_verify_nonce($_POST['wetory_support_settings_libraries_form'], 'wetory_support_settings_libraries_update')) {
            $this->add_settings_message('nonce_verification_failed');
        } else {
            // Handle request data and store them in options table
            if (isset($_POST['wetory-support-libraries'])) {
                update_option('wetory-support-libraries', $_POST['wetory-support-libraries']);
                
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Settings saved.'); ?></p>
                </div>
                <?php
            }
        }
    }

    /**
     * Print the maintenance section text
     * 
     * @since    1.0.0
     */
    public function print_maintenance_section_info() {
        _e('Customize maintenance mode behaviour.', 'wetory-support');
        ?>
        <h4><?php _e('Maintenance mode page', 'wetory-support'); ?></h4>
        <div class="mp-operations-wrapper">          
            <p><?php _e('Plugin provides template for maintenance page which is shown instead of default maintenance notification panel.', 'wetory-support'); ?></p>
            <input type="button" name="create" class="mp-operation button" value="<?php _e('Create maintenance page', 'wetory-support'); ?>" data-working-text="<?php _e('Creating page...', 'wetory-support'); ?>"> 
            <input type="button" name="delete" class="mp-operation button" value="<?php _e('Delete maintenance page', 'wetory-support'); ?>" data-working-text="<?php _e('Deleting page...', 'wetory-support'); ?>">     
            <span class="mp-operation-outcome"></span>
        </div>
        <?php
    }

    /**
     * Print the Widgets section text
     * 
     * @since    1.0.0
     */
    public function print_widgets_section_info() {
        _e('Select widgets you want to use in your website', 'wetory-support');
    }

    /**
     * Print the Shortcodes section text
     * 
     * @since    1.0.0
     */
    public function print_shortcodes_section_info() {
        _e('Select shortcodes you want to use in your website', 'wetory-support');
    }

    /**
     * Print the API key section text
     * 
     * @since    1.0.0
     */
    public function print_apikeys_section_info() {
        _e('Configure API keys for APIs you want to use in your website', 'wetory-support');
    }

    /**
     * Display tab navigation in settings page
     * 
     * @since    1.0.0
     */
    public function display_plugin_admin_settings_tabs($current = 'general') {

        global $tabs;

        echo '<h2 id="wetory-tabs" class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ( $tab == $current ) ? 'nav-tab-active' : '';
            echo "<a class='nav-tab $class' href='?page=wetory-support-settings&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    /**
     * Callback function to display plugin dashboard page
     * @return type
     * 
     * @since    1.0.0
     */
    public function display_plugin_admin_dashboard_cb() {

        $plugin_options = $this->options;

        require_once 'partials/wetory-support-admin-display.php';
    }

    /**
     * Callback function to display plugin settings page
     * @return type
     * 
     * @since    1.0.0
     */
    public function display_plugin_admin_settings_cb() {

        global $tabs;

        /**
         *  Custom form submitted
         */
        if (isset($_POST['libraries_updated']) && $_POST['libraries_updated'] === 'true') {
            $this->update_libraries_settings();
        }

        $tabs = array(
            'general' => __('General', 'wetory-support'),
            'libraries' => __('Libraries', 'wetory-support'),
            'apikeys' => __('API keys', 'wetory-support'),
        );

        // set this var to be used in the settings-display view
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this, 'plugin_settings_messages'));
            do_action('admin_notices', $_GET['error_message']);
        }

        require_once 'partials/wetory-support-admin-display-settings.php';
    }

}
