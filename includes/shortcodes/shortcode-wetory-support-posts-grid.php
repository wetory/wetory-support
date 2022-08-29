<?php

/**
 * Name: Posts Grid
 * Description: Display posts in grid
 * 
 * Link: https://www.wetory.eu/ideas/
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/shortcodes
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Shortcode_Wetory_Support_Posts_Grid extends Wetory_Support_Shortcode {

    /**
     * Create new instance with static properties
     * 
     * [wetory-posts-grid]
     * 
     * Attributes described:
     *  - types     Post types to be displayed, by default "post"    
     *  - status    Only posts with given status, by default "publish"
     *  - order_by  Select criteria for order, by default "date"
     *  - order     Select order direction, by default "DESC"
     *  - count     How many posts to display, by default all posts
     *  - paging    When set to true table is displayed in pages by "n" posts based on "count" attribute
     *  - filter    Display filter over the table or not
     *  - columns   Specify number gird columns
     *  = taxonomy  Specify taxonomy to filter by based on given terms
     *  - terms     Specify terms for filtering posts based on given taxonomy
     * 
     * @since    1.0.0
     */
    public function __construct() {
        // specify shortcode requirements here
        $id = 'wetory-posts-grid';
        $atts = array(
            'types' => 'post',
            'status' => 'publish',
            'order_by' => 'date',
            'order' => 'DESC',
            'count' => '-1',
            'paging' => true,
            'filter' => false,
            'columns' => 2,
            'taxonomy' => 'category',
            'terms' => ''
        );
        $this->before_content = '<section class="wetory-template wetory-posts-grid-wrapper">';
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
    public function get_content($content = null) {

        // Query posts
        $this->query_posts();
        
        // Generate content
        $content = $this->generate_grid();

        // Reset $wp_query
        wp_reset_query();

        return $content;
    }

    /**
     * Generate posts grid
     * 
     * @since 1.1.0
     * 
     * @return string HTML markup of grid
     */
    private function generate_grid() {
        
        $post_type = $this->get_post_type();

        $template_loader = new Wetory_Support_Template_Loader();
        
        $template = $template_loader->locate_template('posts-grid/item-'.$post_type.'.php') ? 'posts-grid/item-'.$post_type : 'posts-grid/item';
        $columns = $this->get_columns();
        
        $data = array(
            'post_type' => $post_type,
            'columns' => $columns
        );
        
        ob_start();
        if (have_posts()) {
            
            // Filter template
            if($this->is_filter_enabled()) {
                $template_loader
                        ->set_template_data($data)
                        ->get_template_part('posts-filter/filter', $post_type);
            }
            
            // Grid items
            echo '<div class="wetory-grid wetory-ajax-post-list row '.$post_type.'" data-loadmore-template="'.$template.'" data-grid-columns="'.$columns.'">';
            while (have_posts()) : the_post();
                $template_loader
                        ->set_template_data($data)
                        ->get_template_part('posts-grid/item', $post_type);
            endwhile;
            wp_reset_postdata();
            echo '</div>';

            // Pagination
            if($this->is_paging_enabled()){
                //$template_loader->get_template_part('pagination', 'loadmore');
            }
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
     * 
     * @return string Template variation based on post type
     */
    private function get_post_type() {
        $atts = $this->shortcode_attributes;
        
        $post_types = explode(',', str_replace(' ', '', $atts['types']));
        $post_type = sizeof($post_types) == 1 ? $post_types[0] : '';

        return $post_type;
    }
    
    /**
     * Evaluating shortcode attribute for posts filter
     * 
     * @since 1.1.0
     * 
     * @return bool
     */
    private function is_filter_enabled():bool {
        $enabled = false;
        if (isset($this->shortcode_attributes['filter'])) {
            $enabled = $this->shortcode_attributes['filter'];
        }
        return $enabled;
    }
    
    /**
     * Evaluating shortcode attribute for posts paging
     * 
     * @since 1.2.0
     * 
     * @return bool
     */
    private function is_paging_enabled():bool {
        $enabled = false;
        if (isset($this->shortcode_attributes['paging'])) {
            $enabled = filter_var($this->shortcode_attributes['paging'], FILTER_VALIDATE_BOOLEAN);
        }
        return $enabled;
    }
    
    /**
     * Evaluating shortcode attribute for number of columns
     * 
     * @since 1.1.0
     * 
     * @return int
     */
    private function get_columns():int {
        $columns = 2;
        if (isset($this->shortcode_attributes['columns'])) {
            $columns = $this->shortcode_attributes['columns'];
        }
        return $columns;
    }

    /**
     * Set global $wp_query based on given attributes
     * 
     * Transform attributes array to WP_Query $query_vars array and create new 
     * WP_Query object which replaces global $wp_query.
     * 
     * Do not forget to call wp_reset_query when custom query is not needed anymore.
     * 
     * @see https://developer.wordpress.org/reference/functions/wp_reset_query/
     * 
     * @since 1.1.0     * 
     */
    private function query_posts() {
        global $wp_query;
        
        $atts = $this->shortcode_attributes;
        
        // Get current page
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        // Construct WP_Query parameters array
        $query_vars = array(
            'post_type' => explode(',', str_replace(' ', '', $atts['types'])),
            'post_status' => explode(',', str_replace(' ', '', $atts['status'])),
            'orderby' => $atts['order_by'],
            'order' => $atts['order'],
            'posts_per_page' => $atts['paging'] ? $atts['count'] : -1,
            'paged' => $atts['paging'],
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
        
        $wp_query = new WP_Query($query_vars);
        
        // Pass query data to JavaScript for further procesing 
        wp_localize_script('wetory-support-ajax', 'wp_query', array(
            'query' => json_encode($wp_query->query),
            'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
            'found_posts' => $wp_query->found_posts,
            'max_num_pages' => $wp_query->max_num_pages,
            'posts_per_page' => get_query_var('posts_per_page'),
        ));
    }

}
