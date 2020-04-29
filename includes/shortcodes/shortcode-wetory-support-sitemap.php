<?php

/**
 * Name: Sitemap
 * Description: Display HTML sitemap based on given attributes
 * 
 * Link: https://www.wetory.eu/ideas/
 *
 * @since      1.0.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/shortcodes
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Shortcode_Wetory_Support_Sitemap extends Wetory_Support_Shortcode {
    
    /**
     * Create new instance with static properties
     * 
     * @since    1.0.0
     */
    public function __construct() {
        // specify shortcode requirements here
        $id = 'wetory-sitemap';
        parent::__construct($id);
    }
    
    /**
     * Registers Wetory Sitemap shortcode.
     * 
     * Basically only calling add_shortcode function with member functions callbacks.
     * https://developer.wordpress.org/reference/functions/add_shortcode/
     * 
     * @since    1.0.0
     */
    public function register() {
        if ($this->use_shortcode()) {
            add_shortcode($this->get_id(), array($this, 'render_shortcode'));
        }
    }

    /**
     * Override parent function for rendering shortcode output
     * 
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     * 
     * @return string HTML markup of shortcode output
     */
    public function render_shortcode($atts, $content = null) {
        return '<div class="wetory-shortcode-container">' . $this->generate_sitemap($atts, $content) . '</div>';
    }

    /**
     * Render HTML sitemap
     *
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     * 
     * @return string HTML sitemap
     */
    private function generate_sitemap($atts, $content = null) {
        
        

        $html = '';

        // Check if some pages to exclude
        $exclude_pages = (isset($atts['exclude']) ? ($atts['exclude']) : null);

        // List the pages
        $html .= $this->get_list_pages($exclude_pages);


        // return the content
        return $html;
    }

    /**
     * Return list of pages
     * 
     * @param bool $is_title_displayed
     * @param bool $is_get_only_private
     * @param bool $display_nofollow
     * @param array $exclude_pages
     * 
     * @return str $return
     */
    private function get_list_pages($exclude_pages = array()) {

        $html = '';
        $args = array(
            'echo' => '0',
            'title_li' => '',
        );

        // exclude some pages ?
        if (!empty($exclude_pages)) {
            $args['exclude'] = $exclude_pages;
        }

        // Gather pages data and return if empty
        $list_pages = wp_list_pages($args);
        if (empty($list_pages)) {
            return '';
        }

        // Iterate pages and get HTML list
        $html .= '<ul class="wetory-sitemap-pages-list">' . "\n";
        $html .= $list_pages;
        $html .= '</ul>' . "\n";

        // return content
        return $html;
    }
}
