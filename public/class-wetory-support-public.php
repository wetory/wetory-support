<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * 
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/public
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

class Wetory_Support_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wetory-support-public.css', array(), $this->version, 'all');
            wp_enqueue_style('boostrap', WETORY_SUPPORT_URL . 'assets/bootstrap/4.4.1/css/bootstrap.css', array(), '4.4.1', 'all');
        } else {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wetory-support-public.min.css', array(), $this->version, 'all');
            wp_enqueue_style('boostrap', WETORY_SUPPORT_URL . 'assets/bootstrap/4.4.1/css/bootstrap.min.css', array(), '4.4.1', 'all');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wetory-support-public.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/wetory-support-ajax.js', array('jquery'), $this->version, true);
            wp_enqueue_script('boostrap', WETORY_SUPPORT_URL . 'assets/bootstrap/4.4.1/js/bootstrap.js', array('jquery'), '4.4.1', true);
        } else {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wetory-support-public.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/wetory-support-ajax.min.js', array('jquery'), $this->version, true);            
            wp_enqueue_script('boostrap', WETORY_SUPPORT_URL . 'assets/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery'), '4.4.1', true);
        }
        // Pass general parameters
        wp_localize_script($this->plugin_name . '-ajax', 'parameters', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

}
