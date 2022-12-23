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
    class Wetory_Support_Ajax
    {

        public function __construct()
        {
            add_filter('wetory_ajax_filter_query', array($this, 'default_ajax_filter_query'), 10, 2);
        }

        public function register_handlers()
        {
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
         * Handler function for Ajax url call 'wetory_support_ajax_update_settings'
         *
         * @since 1.2.1
         *
         */
        public function handle_wetory_support_ajax_update_settings()
        {
            wetory_write_log(__('Updating plugin settings', 'wetory-support'), 'info');
            check_admin_referer('wetory-support-update-' . WETORY_SUPPORT_SETTINGS_OPTION);

            try {
                // Validate before saving to database
                $options = apply_filters('wetory_settings_validate', $_POST[WETORY_SUPPORT_SETTINGS_OPTION]);
                if (is_wp_error($options)) {
                    wp_send_json_error($options, $options->get_error_code());
                    die();
                }

                // Save settings to database
                $updated = update_option(WETORY_SUPPORT_SETTINGS_OPTION, $options);
                wp_send_json_success(esc_html__('Settings updated successfully', 'wetory-support'));
            } catch (Exception $e) {
                wp_send_json_error($e->getMessage(), $e->getCode());
            }
            die();
        }

        public function handle_wetory_support_ajax_reset_settings(){
            wetory_write_log(__('Resetting plugin settings', 'wetory-support'), 'info');
            check_admin_referer('wetory-support-update-' . WETORY_SUPPORT_SETTINGS_OPTION);
            
            try {
                
                $options = array();
                $options = apply_filters('wetory_settings_default', $options);
                $options = apply_filters('wetory_settings_validate', $options);

                if (is_wp_error($options)) {
                    wp_send_json_error($options, $options->get_error_code());
                    die();
                }

                // Save settings to database
                $updated = update_option(WETORY_SUPPORT_SETTINGS_OPTION, $options);
                wp_send_json_success(esc_html__('Settings set to defaults successfully', 'wetory-support'));
            } catch (Exception $e) {
                wp_send_json_error($e->getMessage(), $e->getCode());
            }

            die();
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
        public function handle_wetory_ajax_loadmore()
        {
            global $wp_query;

            // Required data not sent then die
            if (!isset($_POST['query'])) {
                die();
            }

            // Pass some data to template
            $data = array(
                'columns' => isset($_POST['columns']) ? $_POST['columns'] : 2
            );

            // Prepare WP_Query         
            $query = json_decode(stripslashes($_POST['query']), true);
            $query['paged'] = $_POST['page'] + 1;
            $wp_query = new WP_Query($query);

            // Iterate posts and send templated data        
            if (have_posts()) :
                $template_loader = new Wetory_Support_Template_Loader();
                $template = isset($_POST['template']) ? $_POST['template'] : 'content-' . get_post_type();
                while (have_posts()) : the_post();
                    $template_loader
                        ->set_template_data($data)
                        ->get_template_part($template);
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
        public function handle_wetory_ajax_filter()
        {
            global $wp_query;

            // Required data not sent then die
            if (!isset($_POST['query'])) {
                die();
            }

            // Pass some data to template
            $data = array(
                'columns' => isset($_POST['columns']) ? $_POST['columns'] : 2
            );

            // Template loader is needed
            $template_loader = new Wetory_Support_Template_Loader();

            // Prepare WP_Query         
            $query = json_decode(stripslashes($_POST['query']), true);
            $query = apply_filters('wetory_ajax_filter_query', $query, $_POST['form']);

            $wp_query = new WP_Query($query);

            // Iterate posts and send templated data       
            ob_start();
            if (have_posts()) {
                $template = isset($_POST['template']) ? $_POST['template'] : 'content-' . get_post_type();
                while (have_posts()) : the_post();
                    $template_loader
                        ->set_template_data($data)
                        ->get_template_part($template);
                endwhile;
            } else {
                $template_loader->get_template_part('content', 'none');
            }
            $html = ob_get_clean();

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
        public function default_ajax_filter_query(array $query, array $form_data): array
        {
            if (isset($form_data['search']) && $form_data['search'] !== "") {
                $query['s'] = wetory_get_quoted_string($form_data['search']);
            } else {
                unset($query['s']);
            }

            if (isset($form_data['category']) && $form_data['category'] !== "") {
                $query['category_name'] = wetory_get_quoted_string($form_data['category']);
            } else {
                unset($query['category_name']);
            }

            if (isset($form_data['published_from']) && $form_data['published_from'] !== "") {
                $query['date_query']['after'] = $form_data['published_from'];
            } else {
                unset($query['date_query']['after']);
            }
            if (isset($form_data['published_to']) && $form_data['published_to'] !== "") {
                $query['date_query']['before'] = $form_data['published_to'];
            } else {
                unset($query['date_query']['before']);
            }

            /**
             * For after/before, whether exact value should be matched or not
             * @see https://developer.wordpress.org/reference/classes/wp_query/#date-parameters
             */
            $query['date_query']['inclusive'] = true;

            return $query;
        }
    }
}
