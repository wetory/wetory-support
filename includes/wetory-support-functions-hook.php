<?php

/**
 * Wetory helper functions used in hooks.
 *
 * This file contains all functions that are used to hook functionality into WordPress.
 * https://developer.wordpress.org/plugins/hooks/
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
if (!function_exists('wetory_show_debug_info')) {

    /**
     * Function silently adding some debug information into page footer when debugging is enabled.
     * 
     * @global type $template
     */
    function wetory_show_debug_info() {
        if (is_super_admin() && defined('WP_DEBUG') && true === WP_DEBUG) {
            // start writing debug info
            echo '<!-- Wetory Support - Debug Info [';

            // actual page template
            global $template;
            echo 'Template file: ' . basename($template);
            echo ', Template file path: ' . $template;

            // plugin version
            echo ', Plugin version: ' . WETORY_SUPPORT_VERSION;

            echo '] -->';
        }
    }

    add_action('wp_footer', 'wetory_show_debug_info');
}
    