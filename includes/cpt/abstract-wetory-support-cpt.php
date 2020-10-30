<?php

/**
 * Base class for custom post type classes. 
 *
 * This class must be extended for every custom post type within plugin 
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/cpt
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

abstract class Wetory_Support_Cpt {

    use Wetory_Support_Object_File;

    /**
     * Custom post type ID.
     *
     * @since    1.1.0
     * @access   private
     * @var      string  $id  Hold custom post type identificator.
     */
    private $id;

    /**
     * Create new instance
     * 
     * @since    1.1.0     
     * @param string $id Description Post type key. Must not exceed 20 characters and may only contain lowercase alphanumeric characters, dashes, and underscores.
     */
    public function __construct(string $id) {
        $this->id = sanitize_key($id);
    }

    /**
     * Prepare arguments for registering post type. Implementation is always sub-class specific.
     * @since    1.1.0
     */
    protected abstract function get_arguments();

    /**
     * Registers custom post type objects.
     * 
     * Basically calling register_post_type function with actual class properties.
     * https://developer.wordpress.org/reference/functions/register_post_type/
     * 
     * @since    1.1.0
     */
    public function register() {
        //if ($this->use_cpt()) {
         register_post_type($this->id, $this->get_arguments());
//        $this->unregister();
        //}
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
        
        wetory_write_log('Database cleanup performed for custo post type "'.$this->id.'", removed '.sizeof($posts).' posts.', 'info');
    }

    /**
     * Default labels can be changed - need to be overridden with calling parent
     * 
     * Just optional function which can be overridden in subclass to change default labels
     * 
     * @since    1.1.0
     * @return array An array of labels for this post type. 
     */
    protected function labels() {
        $labels = array(
            'name'                  => _x('Post Types', 'Post Type General Name', 'wetory-support'),
            'singular_name'         => _x('Post Type', 'Post Type Singular Name', 'wetory-support'),
            'menu_name'             => __('Post Types', 'wetory-support'),
            'name_admin_bar'        => __('Post Type', 'wetory-support'),
            'archives'              => __('Item Archives', 'wetory-support'),
            'attributes'            => __('Item Attributes', 'wetory-support'),
            'parent_item_colon'     => __('Parent Item:', 'wetory-support'),
            'all_items'             => __('All Items', 'wetory-support'),
            'add_new_item'          => __('Add New Item', 'wetory-support'),
            'add_new'               => __('Add New', 'wetory-support'),
            'new_item'              => __('New Item', 'wetory-support'),
            'edit_item'             => __('Edit Item', 'wetory-support'),
            'update_item'           => __('Update Item', 'wetory-support'),
            'view_item'             => __('View Item', 'wetory-support'),
            'view_items'            => __('View Items', 'wetory-support'),
            'search_items'          => __('Search Item', 'wetory-support'),
            'not_found'             => __('Not found', 'wetory-support'),
            'not_found_in_trash'    => __('Not found in Trash', 'wetory-support'),
            'featured_image'        => __('Featured Image', 'wetory-support'),
            'set_featured_image'    => __('Set featured image', 'wetory-support'),
            'remove_featured_image' => __('Remove featured image', 'wetory-support'),
            'use_featured_image'    => __('Use as featured image', 'wetory-support'),
            'insert_into_item'      => __('Insert into item', 'wetory-support'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'wetory-support'),
            'items_list'            => __('Items list', 'wetory-support'),
            'items_list_navigation' => __('Items list navigation', 'wetory-support'),
            'filter_items_list'     => __('Filter items list', 'wetory-support'),
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
    protected function override_labels(){
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
        return true;
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

}
