<?php

/**
 * Name: Team Members
 * Description: Custom post type team members is used for evidence of people working in team.
 *
 * Link: https://www.wetory.eu/ideas/
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @since 1.2.0
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Validator as Validator;

class Cpt_Wetory_Support_Team_Members extends Wetory_Support_Cpt {

    public function __construct() {
        // post type key
        $id = 'wcpt-team-members';
        parent::__construct($id);
    }

    protected function get_post_type_args() {
        $args = array(
            'label' => __('Team Members', 'wetory-support'),
            'description' => __('Custom post type team members is used for evidence of people working in team.', 'wetory-support'),
            'labels' => $this->labels(),
            'supports' => $this->supports(array('title', 'editor', 'thumbnail')),
            'taxonomies' => array(),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-users',
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'show_in_rest' => false,
            'rewrite' => $this->rewrite(),
            'capability_type' => 'post',
        );

        return $args;
    }

    protected function override_labels() {
        $overriden_labels = array(
            'name' => _x('Team Members', 'Post Type General Name', 'wetory-support'),
            'singular_name' => _x('Team member', 'Post Type Singular Name', 'wetory-support'),
            'menu_name' => __('Team Members', 'wetory-support'),
            'name_admin_bar' => __('Team Members', 'wetory-support'),
            'add_new_item' => __('Create New Team Member', 'wetory-support'),
            'add_new' => __('Create Team Member', 'wetory-support'),
            'featured_image' => __('Photo', 'wetory-support'),
        );
        return $overriden_labels;
    }

    protected function load_sources() {
        add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
    }

    /**
     * Callback function for hook admin_enqueue_scripts
     * https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     */
    public function load_admin_scripts($hook) {
        if (!is_admin()) {
            return;
        }

        // Load only for current post edit post page
        if (in_array($hook, array('post.php', 'post-new.php'))) {
            $screen = get_current_screen();
            if (is_object($screen) && $this->id == $screen->post_type) {
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_style('jquery-ui-datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');
                wp_enqueue_script($this->id . '-script', WETORY_SUPPORT_URL . 'admin/js/cpt/' . $this->id . '.min.js', array('jquery'), WETORY_SUPPORT_VERSION, true);
            }
        }
    }

    public function meta_boxes() {
        // Contact information
        $contact_information_metabox = new Wetory_Support_Metabox(
                $this->id . '_contact_information',
                __('Contact information', 'wetory-support'),
                __('These information will be displayed on website depending on used template.', 'wetory-support'),
                array($this->id),
                'top',
                'high'
        );    
        $contact_information_metabox->add_field(
                array(
                    'name' => 'position',
                    'title' => __('Position', 'wetory-support'),
                    'type' => 'text',
                    'desc' => __('Position in team where this member is working on.', 'wetory-support')
                )
        );
        $contact_information_metabox->add_field(
                array(
                    'name' => 'email',
                    'title' => __('Email contact', 'wetory-support'),
                    'type' => 'text',     
                    'desc' => 'Valid email adress which can be used to contact member.',
                    'placeholder' => 'email@email.com'
                )
        );
        $contact_information_metabox->add_field(
                array(
                    'name' => 'phone',
                    'title' => __('Phone contact', 'wetory-support'),
                    'type' => 'text',   
                    'desc' => 'Preferably use international format of phone number.',
                    'placeholder' => '(+420)'
                )
        );
        $this->add_metabox($contact_information_metabox);
        
        // Social media
        $social_media_links_metabox = new Wetory_Support_Metabox(
                $this->id . '_social_media_links',
                __('Social media links', 'wetory-support'),
                __('These links will be displayed on website depending on used template.', 'wetory-support'),
                array($this->id),
                'normal',
                'high'
        );    
        $social_media_links_metabox->add_field(
                array(
                    'name' => 'facebook',
                    'title' => __('Facebook', 'wetory-support'),
                    'type' => 'text'
                )
        );
        $social_media_links_metabox->add_field(
                array(
                    'name' => 'linkedin',
                    'title' => __('LinkedIn', 'wetory-support'),
                    'type' => 'text'
                )
        );
        $social_media_links_metabox->add_field(
                array(
                    'name' => 'twitter',
                    'title' => __('Twitter', 'wetory-support'),
                    'type' => 'text'
                )
        );        
        $this->add_metabox($social_media_links_metabox);
    }

    public function validate_data($post_id, $data) {

        if (!isset($_POST['post_title']) || $_POST['post_title'] == '') {
            $this->add_validation_error(sprintf(__('"%s" is mandatory field!', 'wetory-support'), __('Post title', 'wetory-support')));
        }
    }

    /**
     * Overriding parent method to include notice board meta in conditions
     */
    public function prepare_filter_query(array $query, array $form_data): array {
        if (isset($query['post_type']) && sizeof($query['post_type']) == 1 && $query['post_type'][0] == $this->id) {
            if (isset($form_data['archive']) && $form_data['archive'] !== "") {
                switch ($form_data['archive']) {
                    case 'archive':
                        $query['meta_query'] = array(
                            array(
                                'key' => 'valid_to',
                                'compare' => '<',
                                'value' => date("Y-m-d") . '',
                            ),
                        );
                        break;
                    case 'actual':
                        $query['meta_query'] = array(
                            'relation' => 'OR',
                            array(
                                'key' => 'valid_to',
                                'value' => false,
                                'type' => 'BOOLEAN'
                            ),
                            array(
                                'key' => 'valid_to',
                                'compare' => 'NOT EXISTS'
                            ),
                            array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'valid_to',
                                    'compare' => '>=',
                                    'value' => date("Y-m-d") . '',
                                ),
                                array(
                                    'key' => 'valid_from',
                                    'compare' => '<=',
                                    'value' => date("Y-m-d") . '',
                                ),
                            ),
                        );
                        break;                   
                    case 'all':
                        unset($query['meta_query']);
                        break;
                    default:
                        break;
                }
            }
        }
        return parent::prepare_filter_query($query, $form_data);
    }

}