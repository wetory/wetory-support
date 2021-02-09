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
        $atts = array(
            'exclude' => '',
        );
        $this->before_content = '<div class="wetory-shortcode-container">';
        $this->after_content  = '</div>';
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
        $sitemap = $this->generate_sitemap($atts, $content);
        return $sitemap;
    }

    /**
     * Render HTML sitemap
     *
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * 
     * @return string HTML sitemap
     */
    private function generate_sitemap($atts) {

        $html = '';
        
        $exclude_pages = (isset($atts['exclude']) ? ($atts['exclude']) : null);
        
        $args = array(
            'echo' => '0',
            'title_li' => '',
        );
        
        if (!empty($exclude_pages)) {
            $args['exclude'] = $exclude_pages;
        }

        $list_pages = wp_list_pages($args);
        if (empty($list_pages)) {
            return '';
        }
        
        $html .= '<ul class="wetory-sitemap-pages-list">' . "\n";
        $html .= $list_pages;
        $html .= '</ul>' . "\n";
        
        return $html;
    }
}
