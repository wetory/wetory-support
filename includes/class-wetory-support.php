<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Widgets controller that is responsible for all Widget objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Widgets_Controller  $plugin_widgets  Maintains and registers all widgets from the plugin.
     */
    protected $plugin_widgets;

    /**
     * Shortcodes controller that is responsible for all Shortcode objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Shortcodes_Controller  $plugin_shortcodes  Maintains and adds all shortcodes from the plugin.
     */
    protected $plugin_shortcodes;

    /**
     * Libraries controller that is responsible for all libraries objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Libraries_Controller  $plugin_libraries  Maintains and import libraries from the plugin.
     */
    protected $plugin_libraries;

    /**
     * API keys controller that is responsible for all API key objects from this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wetory_Support_Apikeys_Controller  $plugin_apikeys  Maintains and uses API keys from the plugin.
     */
    protected $plugin_apikeys;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('WETORY_SUPPORT_VERSION')) {
            $this->version = WETORY_SUPPORT_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wetory-support';

        $this->load_dependencies();
        $this->initialize_objects();
        $this->set_locale();
        $this->register_plugin_content();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register_updater();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wetory_Support_Loader. Orchestrates the hooks of the plugin.
     * - Wetory_Support_i18n. Defines internationalization functionality.
     * - Wetory_Support_Admin. Defines all hooks for the admin area.
     * - Wetory_Support_Public. Defines all hooks for the public side of the site.
     *
     * Also calls function to dynamically require all plugin controller files.
     * @see Wetory_Support::load_controllers()
     * 
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * Load plugin updater
         */
        require_once WETORY_SUPPORT_PATH . 'includes/class-wetory-support-updater.php';

        /**
         * Load trait with useful functions for objects
         */
        require_once WETORY_SUPPORT_PATH . 'includes/trait-wetory-support-object-file.php';

        /**
         * Load helper functions
         */
        require_once WETORY_SUPPORT_PATH . 'includes/wetory-support-functions.php';

        /**
         * Load helper functions for Ajax actions
         */
        require_once WETORY_SUPPORT_PATH . 'includes/wetory-support-functions-ajax.php';

        /**
         * Load helper functions used in hooks
         */
        require_once WETORY_SUPPORT_PATH . 'includes/wetory-support-functions-hook.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once WETORY_SUPPORT_PATH . 'includes/class-wetory-support-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once WETORY_SUPPORT_PATH . 'includes/class-wetory-support-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once WETORY_SUPPORT_PATH . 'admin/class-wetory-support-admin.php';

        /**
         * The class responsible sending notices to admin area.
         */
        require_once WETORY_SUPPORT_PATH . 'admin/class-wetory-support-admin-notices.php';

        /**
         * The class responsible plugin settings.
         */
        require_once WETORY_SUPPORT_PATH . 'admin/class-wetory-support-settings.php';

        /**
         * The class containing rendering callback functions useful in settings pages.
         */
        require_once WETORY_SUPPORT_PATH . 'admin/class-wetory-support-settings-renderer.php';


        /**
         * The class responsible plugin options management.
         */
        require_once WETORY_SUPPORT_PATH . 'includes/class-wetory-support-options.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once WETORY_SUPPORT_PATH . 'public/class-wetory-support-public.php';

        $this->load_controllers();

        $this->loader = new Wetory_Support_Loader();
    }

    /**
     * Load the required controller files for this plugin.
     *
     * Include all files stored in folder ./includes/controllers that meets naming convention
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_controllers() {
        // Require base clas first
        require_once WETORY_SUPPORT_PATH . 'includes/controllers/abstract-wetory-support-controller.php';

        // Then iterate rest base on naming convention
        $controller_files = glob(WETORY_SUPPORT_PATH . 'includes/controllers/class-wetory-support-*-controller.php');
        foreach ($controller_files as $controller_file) {
            require_once $controller_file;
        }
    }

    /**
     * Instantiate controller objects. Objects required for plugin objects manipulation
     * @since    1.0.0
     * @access   private
     */
    private function initialize_objects() {

        $this->plugin_widgets = new Wetory_Support_Widgets_Controller();
        $this->plugin_shortcodes = new Wetory_Support_Shortcodes_Controller();
        $this->plugin_libraries = new Wetory_Support_Libraries_Controller();
        $this->plugin_apikeys = new Wetory_Support_Apikeys_Controller();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wetory_Support_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Wetory_Support_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the content included in plugin
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_plugin_content() {
        $this->loader->add_action('widgets_init', $this->plugin_widgets, 'register');
        $this->loader->add_action('init', $this->plugin_shortcodes, 'register');
        $this->loader->add_action('init', $this->plugin_apikeys, 'register');
    }

    /**
     * Register plugin updater accessing private GitHub repository
     * 
     * @see Wetory_Support_Updater
     *
     * @since    1.0.1
     * @access   private
     */
    private function register_updater() {
        if (is_admin()) {
            new Wetory_Support_Updater(WETORY_SUPPORT_FILE, 'wetory', 'wetory-support', 'b594b6de3ee5a503dd1e0f666424d5e049e4b218'); 
        }
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Wetory_Support_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // This is required for building settings page according to loaded content
        $plugin_settings = new Wetory_Support_Settings(
                $this->get_plugin_libraries(),
                $this->get_plugin_widgets(),
                $this->get_plugin_shortcodes(),
                $this->get_plugin_apikeys(),
                $this->get_plugin_name()
        );

        $this->loader->add_filter('plugin_action_links_' . WETORY_SUPPORT_BASENAME, $plugin_admin, 'add_action_links');

        $this->loader->add_action('admin_menu', $plugin_settings, 'add_settings_pages');
        $this->loader->add_action('admin_init', $plugin_settings, 'register_and_build_fields');

        $this->loader->add_action('admin_enqueue_scripts', $this->plugin_libraries, 'set_admin_area', 9);
        $this->loader->add_action('admin_enqueue_scripts', $this->plugin_libraries, 'register', 10);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Wetory_Support_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_enqueue_scripts', $this->plugin_libraries, 'set_public_area', 9);
        $this->loader->add_action('wp_enqueue_scripts', $this->plugin_libraries, 'register', 10);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The reference to the class that manages external libraries in the plugin.
     *
     * @since     1.0.0
     * @return    Wetory_Support_Widgets_Controller    Manages the widgets in the plugin.
     */
    public function get_plugin_libraries() {
        return $this->plugin_libraries;
    }

    /**
     * The reference to the class that manages the widgets in the plugin.
     *
     * @since     1.0.0
     * @return    Wetory_Support_Widgets_Controller    Manages the widgets in the plugin.
     */
    public function get_plugin_widgets() {
        return $this->plugin_widgets;
    }

    /**
     * The reference to the class that manages the shortcodes in the plugin.
     *
     * @since     1.0.0
     * @return    Wetory_Support_Shortcodes_Controller    Manages the shortcodes in the plugin.
     */
    public function get_plugin_shortcodes() {
        return $this->plugin_shortcodes;
    }

    /**
     * The reference to the class that manages the API keys in the plugin.
     *
     * @since     1.0.0
     * @return    Wetory_Support_Apikeys_Controller    Manages the API keys in the plugin.
     */
    public function get_plugin_apikeys() {
        return $this->plugin_apikeys;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wetory_Support_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}