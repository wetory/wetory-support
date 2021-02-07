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

if (!function_exists('wetory_wpseo_breadcrumb_links')) {

    /**
     * Fixing Yoast SEO breadcrumbs for custom post types. 
     * 
     * @since 1.1.0
     * @param array $breadcrumbs Associative array containing breadcrumbs 
     * @return array Associative array containing breadcrumbs
     */
    function wetory_wpseo_breadcrumb_links($breadcrumbs) {

        if (is_single()) {
            $cpt_object = get_post_type_object(get_post_type());
            if (!$cpt_object->_builtin) {
                
                // Get landing page based on slug
                $landing_page = get_page_by_path($cpt_object->rewrite['slug']);

                // Go through pages in hierarchy and add them to the breadcrumbs
                if ($landing_page->post_parent) {
                    $parents = get_post_ancestors($landing_page->ID);
                    foreach ($parents as $parent) {
                        $id = get_post_field('ID', $parent);
                        array_splice($breadcrumbs, -1, 0, array(
                            array(
                                'url' => get_permalink($id),
                                'text' => get_the_title($id),
                                'id' => $id,
                            )
                        ));
                    }
                }

                // And finally add landing page as well
                array_splice($breadcrumbs, -1, 0, array(
                    array(
                        'url' => get_permalink($landing_page->ID),
                        'text' => get_the_title($landing_page->ID),
                        'id' => $landing_page->ID,
                    )
                ));
            }
        }

        return $breadcrumbs;
    }

    if (is_plugin_active('wordpress-seo/wp-seo.php') || is_plugin_active('wordpress-seo-premium/wp-seo-premium.php')) {
        add_filter('wpseo_breadcrumb_links', 'wetory_wpseo_breadcrumb_links');
    }
}
    