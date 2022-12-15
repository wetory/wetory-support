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
    function wetory_get_prefixed_label(string $text, $delimiter = ' - ')
    {
        if (defined('WETORY_LABEL')) {
            $label = WETORY_LABEL;
        } else {
            $label = 'Wetory';
        }
        return $label . $delimiter . $text;
    }
}

if (!function_exists('wetory_created_by_link')) {

    /**
     * Created by Wetory link
     * 
     * @since      1.0.0
     * @return string
     */
    function wetory_created_by_link()
    {
        echo 'by <a href="https://www.wetory.eu/" target="_blank">wetory</a>';
    }
}

if (!function_exists('wetory_copyright_info')) {

    /**
     * Copyright info about website
     * 
     * @since      1.1.0
     * @return string
     */
    function wetory_copyright_info()
    {
        echo '<a href="' . home_url() . '" title="' . get_bloginfo('description') . '">' . get_bloginfo('name') . '</a> &copy; ' . date("Y");
    }
}

if (!function_exists('wetory_get_quoted_string')) {

    /**
     * Get string surrounded by given character, by default single quote
     * 
     * @since      1.1.0
     * @return string
     */
    function wetory_get_quoted_string(string $string, string $quote = "'")
    {
        return $quote . $string . $quote;
    }
}

if (!function_exists('wetory_date_translate')) {

    /**
     * Superstructure to builtin function strtotime.
     * 
     * This is needed to be bale to work with Czech months 
     * @since 1.1.0
     * @param string $date A date/time string. Valid formats are explained in Date and Time Formats.
     * @param string $target_lang Target language, by default 'en' - english
     * @return int Returns a timestamp on success, false otherwise
     */
    function wetory_date_translate($date, $target_lang = 'en')
    {
        $vocabulary = array(
            'months' => array(
                'cz' => array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"),
                'cz_alt' => array("ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince"),
                'en' => array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")
            ),
        );
        if (get_locale() == 'cs_CZ') {
            if (isset($vocabulary['months'][$target_lang])) {
                $date = str_replace($vocabulary['months']['cz'], $vocabulary['months'][$target_lang], $date);
                $date = str_replace($vocabulary['months']['cz_alt'], $vocabulary['months'][$target_lang], $date);
            }
        }
        return $date;
    }
}

if (!function_exists('wetory_write_log')) {

    /**
     * Custom error writing to debug.log file.
     * 
     * @since      1.0.0
     * @param mixed $log What you want o write to log
     * @param string $severity You can write to log with severity. It is show at the beginning on log message. By default "issue".
     */
    function wetory_write_log($log, $severity = 'issue')
    {
        $prepend = 'Wetory ' . ucwords($severity) . ": ";
        if (is_array($log) || is_object($log)) {
            error_log($prepend);
            error_log(print_r($log, true));
        } else {
            error_log($prepend . $log);
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
    function wetory_var_dump($variable, $die = false)
    {
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
    function wetory_maintenance_page(string $action = 'create')
    {
        $maintenance_page = WP_CONTENT_DIR . '/maintenance.php';
        $maintenance_template = WETORY_SUPPORT_PATH . 'public/partials/wetory-support-maintenance.php';

        if ($action == 'create' && !file_exists(WP_CONTENT_DIR . '/maintenance.php')) {
            wetory_write_log("Creating custom maintenance page " . $maintenance_page . " using template " . $maintenance_template, 'info');

            // Modify headers via PHP
            $maintenance_page_php = '<?php ' . PHP_EOL
                . 'header("' . wp_get_server_protocol() . ' 503 Service Unavailable", true, 503); ' . PHP_EOL
                . 'header("Content-Type: text/html; charset=utf-8"); ' . PHP_EOL
                . 'header("Refresh: 30;url=' . get_site_url() . '"); ' . PHP_EOL
                . 'require_once("./../wp-load.php");'
                . '?>';
            file_put_contents($maintenance_page, $maintenance_page_php);

            // Load maintenance template 
            $maintenance_page_content = file_get_contents($maintenance_template);

            // SAve it to custom maintenance page
            file_put_contents($maintenance_page, $maintenance_page_content, FILE_APPEND);

            // PHP die() needed at very end of maintenance page file
            file_put_contents($maintenance_page, '<?php die();', FILE_APPEND);
        }
        if ($action == 'delete' && file_exists($maintenance_page)) {
            wetory_write_log("Deleting custom maintenance page " . $maintenance_page, 'info');
            unlink($maintenance_page);
        }
    }
}

if (!function_exists('wetory_get_wp_query')) {

    /**
     * Construct query object based on parameters
     * 
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     * 
     * @since      1.1.0
     * @param array $args Parameters for WP_Query object
     * @return \WP_Query
     */
    function wetory_get_wp_query(array $args): WP_Query
    {

        /**
         * Specify some default query parameters
         * @see https://developer.wordpress.org/reference/classes/wp_query/#parameters
         */
        $defaults = array(
            'post_type' => 'any',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
        );

        $args = wp_parse_args($args, $defaults);

        return new WP_Query($args);
    }
}

if (!function_exists('wetory_get_formatted_date')) {

    /**
     * Simple function to get date time in given format or in format from WordPress settings.
     * 
     * @since      1.1.0
     * @param string|DateTime $datetime Date to be formatted
     * @param string $format Format to display the date
     * @return string Formatted date time string
     */
    function wetory_get_formatted_date($datetime, $format = null): string
    {

        $_format = !empty($format) ? $format : get_option('date_format');

        if (is_object($datetime)) {
            $timestamp = $datetime->getTimestamp();
        } else {
            $timestamp = strtotime($datetime);
        }

        return date_i18n($_format, $timestamp);
    }
}

if (!function_exists('wetory_load_more_button')) {

    /**
     * Echoes link to call load more Ajax functionality
     * 
     * @since      1.1.0
     * @global type $wp_query
     */
    function wetory_load_more_button()
    {
        global $wp_query;

        if ($wp_query->max_num_pages > 1) :
            echo '<div class="wetory-ajax-loadmore-wrapper"><button class="wetory-ajax-loadmore">' . __('Load more', 'wetory-support') . '</button></div>';
        endif;
    }
}

if (!function_exists('wetory_get_categories_by_post_type')) {

    function wetory_get_categories_by_post_type($post_type, $args = '')
    {
        $exclude = array();

        //check all categories and exclude
        foreach (get_categories($args) as $category) {
            $posts = get_posts(array('post_type' => $post_type, 'category' => $category->cat_ID));
            if (empty($posts)) {
                $exclude[] = $category->cat_ID;
            }
        }

        //re-evaluate args
        if (!empty($exclude)) {
            if (is_string($args)) {
                $args .= ('' === $args) ? '' : '&';
                $args .= 'exclude=' . implode(',', $exclude);
            } else {
                $args['exclude'] = $exclude;
            }
        }
        return get_categories($args);
    }
}

if (!function_exists('wetory_get_first_image_src')) {

    /**
     * Get src tag of first image in given HTML
     * 
     * @since      1.1.0
     * @param mixed $html HTML content to be analyzed
     * @return boolean|string
     */
    function wetory_get_first_image_src($html)
    {

        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $html, $matches);
        $first_img = isset($matches[1][0]) ? $matches[1][0] : null;

        if (empty($first_img)) {
            return false;
        }

        return $first_img;
    }
}

if (!function_exists('wetory_get_post_thumbnail_url')) {

    /**
     * Get post thumbnail or first image from content
     * 
     * Useful function for returning some thumbnail URL also if not thumbnail is set for post.
     * 
     * @since      1.1.0
     * @param int|WP_Post $post Post ID or WP_Post object. Default is global $post.
     * @param string|array $size Registered image size to retrieve the source for or a flat array of height and width dimensions.
     * @return string Post thumbnail URL or first image from content URL or no image generic image URL from plugin folder
     */
    function wetory_get_post_thumbnail_url($post = null, $size = 'post-thumbnail')
    {
        if (has_post_thumbnail($post)) {
            $img_url = get_the_post_thumbnail_url($post, $size);
        } else {
            $img_url = wetory_get_first_image_src(get_the_content($post));
            if (!$img_url || $img_url == '') {
                $img_url = WETORY_SUPPORT_URL . 'public/images/no-image.png';
            }
        }
        return $img_url;
    }
}
