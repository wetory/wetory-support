<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.wetory.eu/
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Admin_Notices as Notices;

class Wetory_Support_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Object holding abstraction of plugin
     *
     * @since    1.2.1
     * @access   private
     * @var      Wetory_Support $plugin_obj  Instance of main plugin class
     * @see Wetory_Support
     */
    private $plugin_obj;

    /**
     * Associative array holding plugin pages links
     *
     * @since    1.2.1
     * @access   private
     * @var      array    $links Associative array.
     */
    private $links;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      Wetory_Support    $plugin_obj    Instance of main plugin class
     */
    public function __construct($plugin_obj)
    {        
        $this->plugin_obj = $plugin_obj;
        $this->plugin_name = $plugin_obj->get_plugin_name();
        $this->version = $plugin_obj->get_version();

        $this->build_links();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wetory_Support_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wetory_Support_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wetory-support-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wetory_Support_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wetory_Support_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wetory-support-admin.js', array('jquery'), $this->version, false);

        // Pass some parameters to JavaScript
        //        wp_localize_script($this->plugin_name, 'wp_configuration',
        //                array(
        //                    'date_format' => get_option('date_format'),
        //                    'time_format' => get_option('time_format'),
        //                )
        //        );
    }

    /**
     * Helper function for build links used among pages in this plugin.
     * 
     * Links array private member can be easily used in page templates.
     * 
     * @since    1.0.0
     */
    private function build_links()
    {
        $this->links = array();
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
     * Add settings action link to the plugins page.
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
     *
     * @since    1.0.0
     */
    public function plugin_action_links($links)
    {
        $links = array_merge(array('<a href="' . $this->links['settings']['url'] . '">' . __('Settings', 'wetory-support') . '</a>'), $links);
        return $links;
    }

    /**
     * Add options page to admin area
     * 
     * @since    1.2.1
     */
    public function admin_menu()
    {
        global $submenu;

        // https://developer.wordpress.org/reference/functions/add_options_page/
        // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null )
        //        add_options_page(
        //                __('Settings - Wetory', 'wetory-support'),
        //                __('Wetory', 'wetory-support'),
        //                'administrator',
        //                $this->links['settings']['slug'],
        //                array($this, 'admin_settings_page'),
        //                999
        //        );

        // https://developer.wordpress.org/reference/functions/add_menu_page/
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(
            __('Settings - Wetory', 'wetory-support'),
            __('Wetory', 'wetory-support'),
            'administrator',
            $this->links['settings']['slug'],
            array($this, 'admin_settings_page'),
            WETORY_SUPPORT_URL . 'images/dashicon-style-icon.png',
            999
        );

        // https://developer.wordpress.org/reference/functions/add_submenu_page/
        // add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        //        add_submenu_page(
        //                $this->links['settings']['slug'],
        //                __('Settings - Wetory', 'wetory-support'),
        //                __('Settings', 'wetory-support'),
        //                'administrator',
        //                $this->links['settings']['slug'],
        //                array($this, 'admin_settings_page')
        //        );    
    }

    /**
     * Process request to admin settings page
     * 
     * @since    1.2.1
     */
    public function admin_settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permission to perform this operation', 'wetory-support'));
        }
        wetory_write_log($_POST);
        require_once plugin_dir_path(__FILE__) . 'partials/wetory-support-admin-settings.php';
    }
}
