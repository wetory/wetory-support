<?php

/**
 * Name: Notice Board
 * Description: Custom post type notice board is used for official records which is mandatory for city/village websites.
 *
 * Link: https://www.wetory.eu/ideas/
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Cpt_Wetory_Support_Notice_Board extends Wetory_Support_Cpt {

    /**
     * Create new instance with static properties
     * 
     * @since    1.1.0
     */
    public function __construct() {
        $id = 'wcpt-notice-board'; // post type key
        parent::__construct($id);
    }

    /**
     * Specify arguments for registering this post type. See $args section in WP documentation
     * https://developer.wordpress.org/reference/functions/register_post_type/
     * 
     * @since    1.1.0
     * 
     * @return array Arguments for registering post type
     */
    protected function get_arguments() {
        $args = array(
            'label'                 => __('Notice board', 'text_domain'),
            'description'           => __('Custom post type notice board is used for official records which is mandatory for city/village websites.', 'text_domain' ),
            'labels'                => $this->labels(),
            'supports'              => array('title', 'editor', 'revisions', 'custom-fields'),
            'taxonomies'            => array('category', 'post_tag'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-clipboard',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => true,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'rewrite'               => $this->rewrite(),
            'capability_type'       => 'page', 
        );
        
        return $args;
    }
    
    /**
     * Overriding default labels from parent class
     * 
     * @since    1.1.0
     *    
     * @return array An array of labels for this post type. 
     */
    protected function override_labels() {
        $overriden_labels = array(
            'name'                  => _x( 'Notice board', 'Post Type General Name', 'wetory-support' ),
            'singular_name'         => _x( 'Notice board post', 'Post Type Singular Name', 'wetory-support' ),
            'menu_name'             => __( 'Notice board', 'wetory-support' ),
            'name_admin_bar'        => __( 'Notice board', 'wetory-support' ),
        );
        return $overriden_labels;
    }

}
