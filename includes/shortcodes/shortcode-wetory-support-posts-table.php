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
        $table = $this->generate_table($atts, $content);
        return $table;
    }

    /**
     * Generate table based on shortcode attributes
     * 
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     * 
     * @return string HTML markup of table
     */
    private function generate_table($atts, $content = null) {

        $template_loader = new Wetory_Support_Template_Loader();

        // Query posts based on attributes
        $query_vars = $this->prepare_query_vars($atts);
        $posts = new WP_Query($query_vars);
        $GLOBALS['wp_query'] = $posts;

        // Fill in output buffer and return its content
        ob_start();
        if ($posts->have_posts()) {    
            
            // Template varation based on queried post type
            $variation = sizeof($query_vars['post_type']) == 1 ? $query_vars['post_type'][0] : '';
            
            // Construct table from template parts
            $template_loader->get_template_part('posts-table/header', $variation);
            while (have_posts()) : the_post();
                $template_loader->get_template_part('posts-table/row', $variation);
            endwhile;
            $template_loader->get_template_part('posts-table/footer', $variation);
                        
            wp_reset_postdata();
        } else {
            $template_loader->get_template_part('content', 'none');
        }
        wp_reset_query();

        return ob_get_clean();
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
            'paged' => $paged
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
        }

        return $query_vars;
    }

}
