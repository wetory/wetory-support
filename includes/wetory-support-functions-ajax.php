<?php

/**
 * Wetory helper functions that are registered as Ajax callbacks
 *
 * This file contains functions that are registered as callbacks for Ajax actions.
 * File is just wrapper for all functions that has this purpose.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
if (!function_exists('wetory_search_posts_for_options')) {

    /**
     * Function searching in all posts
     * 
     * Querying posts of any post type based on request data search query parameter.
     * Returning array of post id and post title. Which is suitable for select field 
     * options. This function is hooked to Ajax call.
     * 
     * https://developer.wordpress.org/reference/hooks/wp_ajax__requestaction/
     * 
     * @since      1.0.0
     */
    function wetory_search_posts_for_options() {

        // we will pass post IDs and titles to this array
        $return = array();

        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new WP_Query(array(
            's' => $_GET['q'],
            'post_type' => 'any',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        if ($search_results->have_posts()) :
            while ($search_results->have_posts()) : $search_results->the_post();
                // shorten the title a little
                $title = ( mb_strlen($search_results->post->post_title) > 50 ) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                $return[] = array($search_results->post->ID, $title);
            endwhile;
        endif;
        echo json_encode($return);
        die;
    }

    add_action('wp_ajax_wetory_search_posts_for_options', 'wetory_search_posts_for_options'); // wp_ajax_{action}
}

if (!function_exists('wetory_create_maintenance_page')) {

    /**
     * Ajax callback for creating maintenance page
     * 
     * Call to another wetory_ function with proper parameter only
     * 
     * @since      1.0.4
     */
    function wetory_create_maintenance_page() {

        wetory_maintenance_page('create');
        printf(__('File <strong>maintenance.php</strong> created in <strong>%s</strong>', 'wetory-support'), WP_CONTENT_DIR);
        die;
    }

    add_action('wp_ajax_wetory_create_maintenance_page', 'wetory_create_maintenance_page'); // wp_ajax_{action}
}

if (!function_exists('wetory_delete_maintenance_page')) {

    /**
     * Ajax callback for deleting maintenance page
     * 
     * Call to another wetory_ function with proper parameter only
     * 
     * @since      1.0.4
     */
    function wetory_delete_maintenance_page() {

        wetory_maintenance_page('delete');
        printf(__('File <strong>maintenance.php</strong> deleted from <strong>%s</strong>', 'wetory-support'), WP_CONTENT_DIR);
        die;
    }

    add_action('wp_ajax_wetory_delete_maintenance_page', 'wetory_delete_maintenance_page'); // wp_ajax_{action}
}
