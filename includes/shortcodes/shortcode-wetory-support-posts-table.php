<?php

/**
 * Name: Posts Table
 * Description: Display posts in table
 * 
 * Link: https://www.wetory.eu/ideas/
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/shortcodes
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Shortcode_Wetory_Support_Posts_Table extends Wetory_Support_Shortcode {

    /**
     * Create new instance with static properties
     * 
     * [wetory-posts-table]
     * 
     * Attributes described:
     *  - types     Post types to be displayed, by default "post"    
     *  - status    Only posts with given status, by default "publish"
     *  - order_by  Select criteria for order, by default "date"
     *  - order     Select order direction, by default "DESC"
     *  - count     How many posts to display, by default all posts
     *  - paging    When set to true table is displayed in pages by "n" posts based on "count" attribute
     *  - filter    Display filter over the table or not
     *  - columns   Specify columns and their order to display, by default make columns from all available post meta
     *  - taxonomy  Posts can be filtered by taxonomies, by default use categories
     *  - terms     Specify terms for filtering posts based on given taxonomy
     * 
     * @since    1.0.0
     */
    public function __construct() {
        // specify shortcode requirements here
        $id = 'wetory-posts-table';
        $atts = array(
            'types' => 'post',
            'status' => 'publish',
            'order_by' => 'date',
            'order' => 'DESC',
            'count' => '-1',
            'paging' => true,
            'filter' => false,
            'taxonomy' => 'category',
            'terms' => ''
        );
        $this->before_content = '<section class="wetory-support-template posts-table-wrapper">';
        $this->after_content = '</section>';
        parent::__construct($id, $atts);
    }
    
    /**
     * Load required sources for this shortcode.
     */
    protected function load_sources() {
        add_action('wp_enqueue_scripts', array($this, 'load_public_scripts'));
    }
    
    /**
     * Loading scripts for front end
     * 
     * https://developer.wordpress.org/reference/functions/wp_enqueue_script/     * 
     * @since 1.0.0
     */
    public function load_public_scripts() {
        if (is_admin()) {
            return;
        }
        wp_enqueue_script('wetory-support-ajax', WETORY_SUPPORT_URL . 'public/js/wetory-support-ajax.min.js', array('jquery'), WETORY_SUPPORT_VERSION, true);
    }

    /**
     * Override parent function for constructing shortcode content
     * 
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     * 
     * @return string HTML markup of shortcode output
     */
    public function get_content($atts, $content = null) {

        // Query posts
        $query_vars = $this->prepare_query_vars($atts);
        $posts = new WP_Query($query_vars);

        // Generate content
        $template_variation = $this->get_template_variation($atts);
        $content = $this->generate_table($posts, $template_variation);

        return $content;
    }

    /**
     * Generate table for given posts
     * 
     * @since 1.1.0
     *
     * @param WP_Query $posts Instance of WP_Query holding posts etc.
     * @param string $template_variation Variation is used to load correct template based on post type.
     * 
     * @return string HTML markup of table
     */
    private function generate_table(WP_Query $posts, string $template_variation = '') {

        $template_loader = new Wetory_Support_Template_Loader();

        // Construct table from template parts
        ob_start();
        if ($posts->have_posts()) {
            $template_loader->get_template_part('posts-table/header', $template_variation);
            while ($posts->have_posts()) : $posts->the_post();
                $template_loader->get_template_part('posts-table/row', $template_variation);
            endwhile;
            $template_loader->get_template_part('posts-table/footer', $template_variation);
            wp_reset_postdata();
        } else {
            $template_loader->get_template_part('content', 'none');
        }

        return ob_get_clean();
    }

    /**
     * Template variation based on post type attributes.
     * 
     * Variation is used to load correct template based on post type. Return empty
     * string when there are more post types, which means that no variation will
     * be used in templates. 
     * 
     * @since 1.1.0
     * @param array|string $atts Array of shortcode attributes
     * @return string Template variation based on post type
     */
    private function get_template_variation($atts) {
        $post_types = explode(',', str_replace(' ', '', $atts['types']));
        $variation = sizeof($post_types) == 1 ? $post_types[0] : '';

        return $variation;
    }

    /**
     * Transform attributes array to WP_Query $query_vars array
     * 
     * @since 1.1.0
     * @param array $atts Array of attributes
     * 
     * @return array An array of the query variables and their respective values.
     */
    private function prepare_query_vars(array $atts): array {

        // Get current page
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

        // Construct WP_Query parameters array
        $query_vars = array(
            'post_type' => explode(',', str_replace(' ', '', $atts['types'])),
            'post_status' => explode(',', str_replace(' ', '', $atts['status'])),
            'orderby' => $atts['order_by'],
            'order' => $atts['order'],
            'posts_per_page' => $atts['paging'] ? $atts['count'] : -1,
            'paged' => $paged,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'no_found_rows' => !$atts['paging'],
        );

        // Just posts with given taxonomy and terms is specified
        if (isset($atts['terms']) && $atts['terms'] != '') {
            $query_vars['tax_query'] = array(
                array(
                    'taxonomy' => $atts['taxonomy'],
                    'field' => 'slug',
                    'terms' => explode(',', str_replace(' ', '', $atts['terms'])),
                ),
            );
            $query_vars['update_post_term_cache'] = true;
        }

        return $query_vars;
    }

}
