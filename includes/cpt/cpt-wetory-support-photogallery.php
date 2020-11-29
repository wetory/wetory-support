<?php

/**
 * Name: Photo gallery
 * Description: Custom post type photo gallery is used for creating photo galleries on website.
 *
 * Link: https://www.wetory.eu/ideas/
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @since 1.1.0
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */

class Cpt_Wetory_Support_Photogallery extends Wetory_Support_Cpt {
    
    public function __construct() {
        // post type key
        $id = 'wcpt-photogallery'; 
        parent::__construct($id);
    }    

    protected function get_post_type_args() {
        $args = array(
            'label'                 => __('Photo gallery', 'text_domain'),
            'description'           => __('Custom post type photogallery is used for creating photo galleries on website.', 'text_domain' ),
            'labels'                => $this->labels(),
            'supports'              => $this->supports(array('title', 'editor', 'thumbnail', 'author')),
            'taxonomies'            => array('category', 'post_tag'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-camera',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'rewrite'               => $this->rewrite(),
            'capability_type'       => 'post', 
        );
        
        return $args;
    }
    
    protected function override_labels() {
        $overriden_labels = array(
            'name'                  => _x( 'Photo gallery', 'Post Type General Name', 'wetory-support' ),
            'singular_name'         => _x( 'Photo gallery post', 'Post Type Singular Name', 'wetory-support' ),
            'menu_name'             => __( 'Photo galleries', 'wetory-support' ),
            'name_admin_bar'        => __( 'Photo galleries', 'wetory-support' ),
        );
        return $overriden_labels;
    }
    
    protected function load_sources() {
        add_action('admin_enqueue_scripts', array($this, 'load_scripts'));
    }
    
    /**
     * Callback function for hook admin_enqueue_scripts
     * https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     */
    public function load_scripts($hook) {
        // Load only for current post edit post page
        if (in_array($hook, array('post.php', 'post-new.php'))) {
            $screen = get_current_screen();
            if (is_object($screen) && $this->id == $screen->post_type) {                
                // wp_enqueue_script($this->id . '-script', WETORY_SUPPORT_URL . 'admin/js/cpt/' . $this->id . '.min.js', array('jquery'), WETORY_SUPPORT_VERSION, true);
            }
        }
    }

    public function validate_data($post_id, $data) {
        
        if(!isset($_POST['post_title']) || $_POST['post_title'] == ''){
            $this->add_validation_error(sprintf(__('"%s" is mandatory field!', 'wetory-support'), __('Post title', 'wetory-support')));
        }
    }

}
