<?php

/**
 * Name: Child Pages
 * Description: Display formatted list of child pages for page where used
 * 
 * Link: https://www.wetory.eu/ideas/
 *
 * @since      1.2.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/shortcodes
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Shortcode_Wetory_Support_Child_Pages extends Wetory_Support_Shortcode {

    /**
     * Create new instance with static properties
     * 
     * @since    1.2.0
     */
    public function __construct() {
        // specify shortcode requirements here
        $id = 'wetory-child-pages';
        $atts = array(
           // Add some attributes here if needed
        );
        $this->before_content = '<div class="wetory-shortcode-container">';
        $this->after_content = '</div>';
        parent::__construct($id, $atts);
    }

    /**
     * Override parent function for constructing shortcode content
     * 
     * @since 1.2.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     * 
     * @return string HTML markup of shortcode output
     */
    public function get_content($content = null) {
        $content = $this->generate_child_pages();
        return $content;
    }

    /**
     * Render HTML for child pages
     *
     * @since 1.2.0
     * 
     * @return string HTML for child pages
     */
    private function generate_child_pages() {
        global $post;
        
        $html = '';

        $child_of = (isset($post->ID) ? ($post->ID) : null);

        $args = array(
            'echo' => '0',
            'title_li' => '',
        );

        if (!empty($child_of)) {
            $args['child_of'] = $child_of;
        }

        $list_pages = wp_list_pages($args);
        if (empty($list_pages)) {
            return '';
        }

        $html .= '<ul class="wetory-chold-pages-list">' . "\n";
        $html .= $list_pages;
        $html .= '</ul>' . "\n";

        return $html;
    }

}
