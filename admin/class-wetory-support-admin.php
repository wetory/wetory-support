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
class Wetory_Support_Admin {

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
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wetory-support-admin.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

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
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wetory-support-admin.min.js', array('jquery'), $this->version, false);

        // Pass some parameters to JavaScript
        wp_localize_script($this->plugin_name, 'wp_configuration',
                array(
                    'ajax_url'    => admin_url( 'admin-ajax.php'),
                    'date_format' => get_option('date_format'),
                    'time_format' => get_option('time_format'),
                )
        );
    }

    /**
     * Add settings action link to the plugins page.
     * https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
     *
     * @since    1.0.0
     */
    public function add_action_links($links) {
        $dashboard_url = esc_url(
                add_query_arg(
                        'page',
                        $this->plugin_name,
                        get_admin_url() . 'admin.php'
                )
        );
        $settings_url = esc_url(
                add_query_arg(
                        'page',
                        $this->plugin_name . '-settings',
                        get_admin_url() . 'admin.php'
                )
        );
        $links = array_merge(
                array('<a href="' . $dashboard_url . '">' . __('Dashboard', 'wetory-support') . '</a>'),
                array('<a href="' . $settings_url . '">' . __('Settings', 'wetory-support') . '</a>'),
                $links
        );
        return $links;
    }

}
