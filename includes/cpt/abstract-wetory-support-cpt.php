<?php

/**
 * Base class for custom post type classes. 
 *
 * This class must be extended for every custom post type within plugin. Its purpose
 * is simplifying development by easy adding of new post types.
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @author     Tomáš Rybnickı <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;
use Wetory_Support_Admin_Notices as Notices;

abstract class Wetory_Support_Cpt {

    use Wetory_Support_Object_File;

    /**
     * Custom post type ID.
     *
     * @since    1.1.0
     * @access   protected
     * @var      string  $id  Hold custom post type identificator.
     */
    protected $id;

    /**
     * Array of meta boxes
     *
     * @since    1.1.0
     * @access   protected
     * @var      array  $metaboxes  Hold meta boxes as objects in array.
     */
    protected $metaboxes = array();
    protected $validation_errors = array();

    /**
     * Create new instance
     * 
     * @since    1.1.0     
     * @param string $id Description Post type key. Must not exceed 20 characters and may only contain lowercase alphanumeric characters, dashes, and underscores.
     */
    public function __construct(string $id) {
        $this->id = sanitize_key($id);
        $this->validation_errors = array();
    }

    /**
     * Registers custom post type objects.
     * 
     * Basically calling register_post_type function with actual class properties,
     * then adding meta boxes to post edit page.
     * 
     * https://developer.wordpress.org/reference/functions/register_post_type/
     * https://developer.wordpress.org/reference/hooks/add_meta_boxes/
     * 
     * @since    1.1.0
     */
    public function register() {
        if ($this->use_cpt()) {
            register_post_type($this->id, $this->get_post_type_args());
            $this->initialize();
        }
    }

    /**
     * Wrapping up adding post type actions to WordPress hooks.
     * 
     * Includes metaboxes, saving of post etc.
     * 
     * @since    1.1.0
     */
    public function initialize() {
        $this->load_sources();
        add_action('load-post.php', array($this, 'meta_boxes'));
        add_action('load-post-new.php', array($this, 'meta_boxes'));
        add_action('save_post_' . $this->id, array($this, 'save_post'), 10, 2);
        add_action('pre_post_update', array($this, 'validation'), 10, 2);
    }

    /**
     * Unregistering custom post type.
     * 
     * https://developer.wordpress.org/reference/functions/unregister_post_type/
     * 
     * @see Wetory_Support_Cpt::database_cleanup()
     * @since 1.1.0
     */
    public function unregister() {
        unregister_post_type($this->id);
        $this->database_cleanup();
    }

    /**
     * Prepare arguments for registering post type. 
     * 
     * Specify arguments for registering this post type. See $args section in WP documentation
     * https://developer.wordpress.org/reference/functions/register_post_type/
     * 
     * Implementation is always sub-class specific.
     * 
     * @since    1.1.0
     * 
     * @return array Arguments for registering post type
     */
    protected abstract function get_post_type_args();

    /**
     * Add meta boxes to post edit page. 
     * 
     * Override this function in child class to specify meta boxes for custom 
     * post type. If no metaboxes needed just return. 
     * 
     * Calling add_meta_box function from WordPress core with custom callback.
     * https://developer.wordpress.org/reference/functions/add_meta_box/
     * 
     * @since    1.1.0
     */
    public function meta_boxes() {
        return;
    }

    /**
     * Subclass specific validation method
     * 
     * Implementation is always sub-class specific.
     * 
     * @since 1.1.0
     * @param int $post_id Post ID
     * @param array $data Array of unslashed post data
     * 
     * @return bool|string 
     */
    public abstract function validate_data($post_id, $data);

    /**
     * Do validation
     * 
     * Fires immediately before an existing post is updated in the database.
     * 
     * @since 1.1.0
     * @param int $post_id Post ID
     * @param array $data Array of unslashed post data
     * 
     * @return bool|string 
     */
    public function validation($post_id, $data) {

        // Run this validation only for this custom post type
        if ($data['post_type'] !== $this->id) {
            return;
        }

        // Run validation only when publishing post
        if (isset($_POST['post_status']) && $_POST['post_status'] == 'publish') {

            $this->validation_errors = array();

            $this->validate_data($post_id, $data);

            if (sizeof($this->validation_errors) > 0) {
                foreach ($this->validation_errors as $error) {
                    Notices::error($error);
                }
                header('Location: ' . get_edit_post_link($post_id, 'redirect'));
                exit;
            }
        }
    }

    /**
     * Handle event when user save post. Contains saving of post meta etc.
     * 
     * @since    1.1.0
     * 
     * @param int $post_id post ID
     * @param WP_POST $post the post object.
     * 
     * @return int Post ID
     */
    public function save_post($post_id, $post) {

        if (!$this->save_post_allowed($post)) {
            return $post_id;
        }

        if (!$this->check_capabilities($post)) {
            wp_die(__('You are not allowed to modify this post!', 'wetory-support'), __('Unauthorized action', 'wetory-support'), array('back_link' => true));
            return;
        }

        // Iterate metaboxes and save values
        foreach ($this->metaboxes as $metabox) {
            $metabox->save_meta_values($post_id, $post);
        }
    }

    /**
     * Check if this post is allowed to run save workflow.
     * 
     * This contains post status, type etc. This is neede to prevent unnecesary
     * work.
     * 
     * @since    1.1.0
     * 
     * @param WP_POST $post the post object.
     * 
     * @return boolean True if allowed
     */
    private function save_post_allowed($post) {
        $allow = true;

        $restrict = array('auto-draft', 'revision', 'acf-field', 'acf-field-group');
        if (in_array($post->post_type, $restrict)) {
            $allow = false;
        }

        return $allow;
    }

    /**
     * Check user capabilities to prevent unauthorized operations.
     * 
     * https://developer.wordpress.org/reference/functions/current_user_can/ 
     * 
     * @since 1.1.0
     * @param WP_POST $post the post object.
     * @return boolean True when capabilities check passed, false when not
     */
    private function check_capabilities($post) {
        $post_type = get_post_type_object($post->post_type);
        if (!current_user_can($post_type->cap->edit_post, $post->ID)) {
            return false;
        }
        return true;
    }

    /**
     * Remove custom post type posts from database.
     *  
     * https://developer.wordpress.org/reference/classes/wpdb/
     * https://developer.wordpress.org/reference/functions/wp_delete_post/
     * 
     * @since 1.1.0     * 
     * 
     * @global wpdb $wpdb WordPress database access abstraction class.
     */
    public function database_cleanup() {
        global $wpdb;

        $posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = '" . $this->id . "'");
        foreach ((array) $posts as $post) {
            wp_delete_post($post, true);
        }

        wetory_write_log('Database cleanup performed for custo post type "' . $this->id . '", removed ' . sizeof($posts) . ' posts.', 'info');
    }

    /**
     * Default labels can be changed
     * 
     * Specify changed values for array keys in function override_labels.
     * d
     * @since    1.1.0
     * @return array An array of labels for this post type. 
     */
    protected function labels() {
        $labels = array(
            'name' => _x('Post Types', 'Post Type General Name', 'wetory-support'),
            'singular_name' => _x('Post Type', 'Post Type Singular Name', 'wetory-support'),
            'menu_name' => __('Post Types', 'wetory-support'),
            'name_admin_bar' => __('Post Type', 'wetory-support'),
            'archives' => __('Item Archives', 'wetory-support'),
            'attributes' => __('Item Attributes', 'wetory-support'),
            'parent_item_colon' => __('Parent Item:', 'wetory-support'),
            'all_items' => __('All Items', 'wetory-support'),
            'add_new_item' => __('Add New Item', 'wetory-support'),
            'add_new' => __('Add New', 'wetory-support'),
            'new_item' => __('New Item', 'wetory-support'),
            'edit_item' => __('Edit Item', 'wetory-support'),
            'update_item' => __('Update Item', 'wetory-support'),
            'view_item' => __('View Item', 'wetory-support'),
            'view_items' => __('View Items', 'wetory-support'),
            'search_items' => __('Search Item', 'wetory-support'),
            'not_found' => __('Not found', 'wetory-support'),
            'not_found_in_trash' => __('Not found in Trash', 'wetory-support'),
            'featured_image' => __('Featured Image', 'wetory-support'),
            'set_featured_image' => __('Set featured image', 'wetory-support'),
            'remove_featured_image' => __('Remove featured image', 'wetory-support'),
            'use_featured_image' => __('Use as featured image', 'wetory-support'),
            'insert_into_item' => __('Insert into item', 'wetory-support'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'wetory-support'),
            'items_list' => __('Items list', 'wetory-support'),
            'items_list_navigation' => __('Items list navigation', 'wetory-support'),
            'filter_items_list' => __('Filter items list', 'wetory-support'),
        );

        return array_replace($labels, $this->override_labels());
    }

    /**
     * Change default labels - need to be overridden
     * 
     * Just optional function which can be overridden in subclass to change default
     * post type labels.
     * 
     * @since 1.1.0
     * @return array An array of labels for this post type. 
     */
    protected function override_labels() {
        return array();
    }

    /**
     * Specify rewrite rules for custom permalinks
     * 
     * This can be overridden in sub-class. To specify rewrite rules, an array 
     * can be passed - see register_post_type documentation section 'rewrite'
     * https://developer.wordpress.org/reference/functions/register_post_type/#parameters
     * 
     * @since 1.1.0
     * @return boolean Defaults to true, using $post_type as slug
     */
    protected function rewrite() {
        $options = $this->get_options();
        if (!empty($options['rewrite-slug'])) {
            return array(
                'slug' => $options['rewrite-slug'],
                'with_front' => false
            );
        }
        return true;
    }

    /**
     * Specify core feature(s) the post type supports.
     * 
     * Combine statically given supports from subclass with post type settings
     * from options.
     * 
     * Serves as an alias for calling add_post_type_support() directly. 
     * Core features include 'title', 'editor', 'comments', 'revisions', 'trackbacks', 
     * 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', and 'post-formats'.
     * 
     * https://developer.wordpress.org/reference/functions/register_post_type/#parameters
     * 
     * @since 1.1.0
     * 
     * @param array $supports Specify supported features except 'comments', 'revisions' and 'excerpt' which are specified in plugin configuration.
     * @return array Core feature(s) the post type supports.
     */
    protected function supports($supports = array()) {
        $options = $this->get_options();
        $features = array('comments', 'excerpt', 'revisions');
        foreach ($features as $feature) {
            if (!empty($options[$feature]) && $options[$feature] == 'on') {
                array_push($supports, $feature);
            }
        }
        return $supports;
    }

    /**
     * Load some additional sources - need to be overridden
     * 
     * Just optional function which can be overridden in subclass to load some
     * JavaScript or other stuff if needed for widget functionality.  
     * 
     * @since 1.1.0
     */
    protected function load_sources() {
        return;
    }

    /**
     * Instantiate subclass extending this abstract class.
     * 
     * @since    1.1.0
     * @param string $subclass Sub-class name
     * @return \Wetory_Support_Cpt New instance of given object
     */
    public static function create_instance(string $subclass): Wetory_Support_Cpt {
        return new $subclass();
    }

    /**
     * Public access function to private member id
     * 
     * @since    1.1.0
     * @return string
     */
    public function get_id(): string {
        return $this->id;
    }

    /**
     * Returns count number of posts published of a post type
     * 
     * @see https://developer.wordpress.org/reference/functions/wp_count_posts/
     * @since    1.1.0
     * @return int
     */
    public function get_published_posts_count() {
        return isset(wp_count_posts($this->id)->publish) ? wp_count_posts($this->id)->publish : 0;
    }

    /**
     * Add metabox object to this custom post type
     * 
     * @since    1.1.0
     * @param Wetory_Support_Metabox $metabox
     */
    public function add_metabox(Wetory_Support_Metabox $metabox) {
        $this->metaboxes[] = $metabox;
    }

    /**
     * Add error message to validation errors.
     * 
     * @since 1.1.0
     * @param string $message Error message
     */
    protected function add_validation_error($message) {
        $this->validation_errors[] = $message;
    }

    /**
     * Options caller. 
     * 
     * Returns false if no options found in database.
     * 
     * @since    1.1.0
     * @see Plugin_Options::get_cpt_options($cpt)
     * @return boolean
     */
    public function get_options() {
        $options = Plugin_Options::get_cpt_options($this->id);

        return $options;
    }

    /**
     * Check if custom post type is configured for use. 
     * 
     * @since    1.1.0
     * @see Plugin_Options::use_cpt($cpt)
     * @return boolean
     */
    public function use_cpt() {
        return Plugin_Options::use_cpt($this->id);
    }

    /**
     * Returns this object as associative array
     * 
     * @since    1.1.0
     * @return array Object presentation as associative array
     */
    public function to_array() {
        $result = array(
            'id' => $this->id,
            'name' => $this->get_post_type_args()['label'],
            'description' => $this->get_post_type_args()['description'],
            'published-posts' => $this->get_published_posts_count(),
            'meta' => $this->get_meta()
        );
        return $result;
    }

}
