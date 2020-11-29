<?php

/**
 * Name: Notice Board
 * Description: Custom post type notice board is used for official records which is mandatory for city/village websites.
 *
 * Link: https://www.wetory.eu/ideas/
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @since 1.1.0
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Validator as Validator;

class Cpt_Wetory_Support_Notice_Board extends Wetory_Support_Cpt {
    
    public function __construct() {
        // post type key
        $id = 'wcpt-notice-board'; 
        parent::__construct($id);
    }    

    protected function get_post_type_args() {
        $args = array(
            'label'                 => __('Notice board', 'text_domain'),
            'description'           => __('Custom post type notice board is used for official records which is mandatory for city/village websites.', 'text_domain' ),
            'labels'                => $this->labels(),
            'supports'              => $this->supports(array('title', 'editor', 'author', 'custom-fields')),
            'taxonomies'            => array('category', 'post_tag'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-clipboard',
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
            'name'                  => _x( 'Notice board', 'Post Type General Name', 'wetory-support' ),
            'singular_name'         => _x( 'Notice board post', 'Post Type Singular Name', 'wetory-support' ),
            'menu_name'             => __( 'Notice board', 'wetory-support' ),
            'name_admin_bar'        => __( 'Notice board', 'wetory-support' ),
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
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_style('jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');
                wp_enqueue_script($this->id . '-script', WETORY_SUPPORT_URL . 'admin/js/cpt/' . $this->id . '.min.js', array('jquery'), WETORY_SUPPORT_VERSION, true);
            }
        }
    }

    public function meta_boxes() {
        $post_validity_metabox = new Wetory_Support_Metabox(
                $this->id.'_post_validity', 
                __( 'Post validity', 'wetory-support' ), 
                __( 'This post supports limited validity in time. Select limiting dates.', 'wetory-support' ), 
                array($this->id), 
                'side', 
                'high'
        );
        $post_validity_metabox->add_field(
                array(
                    'name' => 'valid_from',
                    'title' => __( 'Valid from', 'wetory-support' ),
                    'type' => 'date',
                    'required' => true,
                    'desc' => __( 'Specify start date of post validity', 'wetory-support' )
                )
        );
        $post_validity_metabox->add_field(
                array(
                    'name' => 'valid_to',
                    'title' => __( 'Valid to', 'wetory-support' ),
                    'type' => 'date',
                    'desc' => __( 'Specify end date of post validity', 'wetory-support' )
                )
        );
        $this->add_metabox($post_validity_metabox);
    }

    public function validate_data($post_id, $data) {
        
        if(!isset($_POST['post_title']) || $_POST['post_title'] == ''){
            $this->add_validation_error(sprintf(__('"%s" is mandatory field!', 'wetory-support'), __('Post title', 'wetory-support')));
        }
        
        if(isset($_POST['valid_from']) && $_POST['valid_from'] !== ''){
            if(!Validator::is_date($_POST['valid_from'])){
                $this->add_validation_error(sprintf(__('"%s" value is not valid date!', 'wetory-support'), __('Valid from', 'wetory-support')));
            }
        } else {
            $this->add_validation_error(sprintf(__('"%s" value is required!', 'wetory-support'), __('Valid from', 'wetory-support')));
        }
        
        if(isset($_POST['valid_to']) && $_POST['valid_to'] !== ''){
            if(!Validator::is_date($_POST['valid_to'])){
                $this->add_validation_error(sprintf(__('"%s" value is not valid date!', 'wetory-support'), __('Valid to', 'wetory-support')));
            }
        }
        
        if(isset($_POST['valid_to']) && $_POST['valid_to'] !== '' && isset($_POST['valid_from']) && $_POST['valid_from'] !== '') {
            if($_POST['valid_to'] < $_POST['valid_from']) {
                $this->add_validation_error(sprintf(__('"%s" value must be higher or equal to "%s"!', 'wetory-support'), __('Valid to', 'wetory-support'), __('Valid from', 'wetory-support')));
            }
        }
    }

}
