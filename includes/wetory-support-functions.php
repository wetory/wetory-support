<?php

/**
 * Wetory helper functions.
 *
 * This file contains all publicly accessible API functions used in and for Wetory plugins.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
if (!function_exists('wetory_get_prefixed_label')) {

    /**
     * Get given text translated and prefixed by wetory label
     * 
     * @since      1.0.0
     * @param string $text Text to be translated and prefixed
     * @param string $delimiter Optional delimiter between label and text. Default " - "
     * @return string 
     */
    function wetory_get_prefixed_label(string $text, $delimiter = ' - ') {
        if (defined('WETORY_LABEL')) {
            $label = WETORY_LABEL;
        } else {
            $label = 'Wetory';
        }
        return $label . $delimiter . $text;
    }

}

if (!function_exists('wetory_get_created_by_link')) {

    /**
     * Created by Wetory link
     * 
     * @since      1.0.0
     * @return string
     */
    function wetory_get_created_by_link() {
        return 'by <a href="https://www.wetory.eu/" target="_blank">wetory</a>';
    }

}

if (!function_exists('wetory_write_log')) {

    /**
     * Custom error writing to debug.log file.
     * 
     * @since      1.0.0
     * @param mixed $log What you want o write to log
     */
    function wetory_write_log($log) {
        if (is_array($log) || is_object($log)) {
            error_log("Wetory Issue: ");
            error_log(print_r($log, true));
        } else {
            error_log("Wetory Issue: " . $log);
        }
    }

}

if (!function_exists('wetory_var_dump')) {

    /**
     * Custom var_dump adding more informaiton to PHP built-in var_dump
     * 
     * @since      1.0.0
     * 
     * @param mixed $variable The variable you want to dump
     * @param boolean $die PHP process will die when set to true
     */
    function wetory_var_dump($variable, $die = false) {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        if ($die) {
            die();
        }
    }

}

if (!function_exists('wetory_maintenance_page')) {

    /**
     * Create custom maintenance page based on template file
     * 
     * Template file - /partials/wetory-support-maintenance.php
     * @param string $action Values "create" or "delete". When set to "delete" maintenance.php file is deleted from server
     */
    function wetory_maintenance_page(string $action = 'create') {
        $maintenance_page = WP_CONTENT_DIR . '/maintenance.php';
        $maintenance_template = WETORY_SUPPORT_PATH . 'public/partials/wetory-support-maintenance.php';
        
        if ($action == 'create' && !file_exists(WP_CONTENT_DIR . '/maintenance.php')) {
            wetory_write_log("Creating custom maintenance page " . $maintenance_page);

            // Modify headers via PHP
            $maintenance_page_php = '<?php ' . PHP_EOL
                    . 'header("' . wp_get_server_protocol() . ' 503 Service Unavailable", true, 503); ' . PHP_EOL
                    . 'header("Content-Type: text/html; charset=utf-8"); ' . PHP_EOL
                    . 'header("Refresh: 30;url=' . get_site_url() . '"); ' . PHP_EOL
                    . '?>';
            file_put_contents($maintenance_page, $maintenance_page_php);

            // Load maintenancetemplate info buffer and push it to maintenance page
            ob_start();
            include $maintenance_template;
            $content = ob_get_clean();
            file_put_contents($maintenance_page, $content, FILE_APPEND);

            // PHP die() needed at very end of maintenance page file
            file_put_contents($maintenance_page, '<?php die();', FILE_APPEND);
        }
        if ($action == 'delete' && file_exists($maintenance_page)) {
            wetory_write_log("Deleting custom maintenance page " . $maintenance_page);
            unlink($maintenance_page);
        }
    }

}