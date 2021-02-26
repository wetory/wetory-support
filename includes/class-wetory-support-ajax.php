<?php

if (!class_exists('Wetory_Support_Ajax')) {


    /**
     * Ajax handlers class
     *
     * Class contains methods that are registered as AJAX handlers based on naming
     * convention. 
     * 
     * @link       https://www.wetory.eu/
     * @since      1.1.0
     * @package    wetory_support
     * @subpackage wetory_support/includes
     * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
     */
    class Wetory_Support_Ajax {
        
        public function __construct() {
            add_filter('wetory_ajax_filter_query', array($this, 'default_ajax_filter_query'), 10, 2);
        }

        public function register_handlers() {
            $class_methods = get_class_methods(self::class);

            foreach ($class_methods as $method_name) {
                if (substr($method_name, 0, 7) === "handle_") {
                    $handler_name = substr($method_name, 7, strlen($method_name));
                    add_action('wp_ajax_' . $handler_name, array($this, $method_name));
                    add_action('wp_ajax_nopriv_' . $handler_name, array($this, $method_name));
                }
            }
        }

        /**
         * Handler function for Ajax url call 'wetory_ajax_loadmore'
         * 
         * Assuming that call contains query for constructing WP_Query object, if so
         * then showing posts via templates while iterating WP_Query results.
         * @since 1.1.0
         * 
         * @global WP_Query $wp_query
         */
        public function handle_wetory_ajax_loadmore() {
            global $wp_query;

            // Required data not sent then die
            if (!isset($_POST['query'])) {
                die();
            }

            // Prepare WP_Query         
            $query = json_decode(stripslashes($_POST['query']), true);
            $query['paged'] = $_POST['page'] + 1;
            $wp_query = new WP_Query($query);

            // Iterate posts and send templated data        
            if (have_posts()) :
                $template_loader = new Wetory_Support_Template_Loader();
                $template = isset($_POST['template']) ? $_POST['template'] : 'content-' . get_post_type();
                while (have_posts()): the_post();
                    $template_loader->get_template_part($template);
                endwhile;
            endif;
            die;
        }

        /**
         * Handler function for Ajax url call 'wetory_ajax_filter'
         * 
         * Assuming that call contains query for constructing WP_Query object, if so
         * then showing posts via templates while iterating WP_Query results.
         * @since 1.1.0
         * 
         * @global WP_Query $wp_query
         */
        public function handle_wetory_ajax_filter() {
            global $wp_query;

            // Required data not sent then die
            if (!isset($_POST['query'])) {
                die();
            }

            // Prepare WP_Query         
            $query = json_decode(stripslashes($_POST['query']), true);
            $query = apply_filters('wetory_ajax_filter_query', $query, $_POST['form']);
            
            wetory_write_log($query, 'wetory_ajax_filter');

            $wp_query = new WP_Query($query);

            // Iterate posts and send templated data        
            if (have_posts()) :
                $template_loader = new Wetory_Support_Template_Loader();
                $template = isset($_POST['template']) ? $_POST['template'] : 'content-' . get_post_type();
                ob_start();
                while (have_posts()): the_post();
                    $template_loader->get_template_part($template);
                endwhile;
                $html = ob_get_clean();
            endif;

            echo json_encode(array(
                'query' => json_encode($wp_query->query),
                'max_num_pages' => $wp_query->max_num_pages,
                'found_posts' => $wp_query->found_posts,
                'posts_per_page' => get_query_var('posts_per_page'),
                'html' => $html
            ));

            die;
        }

        /**
         * Include default values to query used in 'wetory_ajax_filter' handler
         * 
         * @param array $query Query that is used to create WP_Query in 'wetory_ajax_filter' handler.
         * @param array $form_data Data send by AJAX request, usually contains form input fields
         * 
         *  @since    1.1.0
         * 
         * @return array Modified query
         */
        public function default_ajax_filter_query(array $query, array $form_data): array {
            if (isset($form_data['search']) && $form_data['search'] !== "") {
                $query['s'] = wetory_get_quoted_string($form_data['search']);
            }
            return $query;
        }

    }

}