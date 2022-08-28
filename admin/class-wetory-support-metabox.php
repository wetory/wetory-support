<?php

/**
 * Meta box class for managing custom meta boxes in plugin.
 *
 * Instantiate class to render, validate, save and retrieve custom meta box
 * values with meta values from database.
 * 
 * https://developer.wordpress.org/plugins/metadata/custom-meta-boxes/
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

use Wetory_Support_Metabox_Renderer as Metabox_Renderer;

class Wetory_Support_Metabox {

    /**
     * Meta key
     * @var string
     */
    public $meta_key = '';

    /**
     * Meta values
     * @access protected
     * @var array
     */
    protected $meta_values = array();

    /**
     * Array of fields 
     * array(
     *  'field_name' => array(),
     * )
     * @access protected
     * @var array
     */
    protected $fields = array();

    /**
     * Data gotten from POST
     * @access protected
     * @var array
     */
    protected $posted_data = array();

    /**
     * Meta box Title
     */
    protected $title = '';

    /**
     * Meta box Description
     */
    protected $description = '';

    /**
     * Meta box ID
     */
    protected $ID = '';

    /**
     * Array of post types for which we allow the meta box
     */
    protected $post_types = array();

    /**
     * Post ID used to save or retrieve the meta values
     */
    protected $post_id = 0;

    /**
     * Meta box context
     */
    protected $context = '';

    /**
     * Meta box priority
     */
    protected $priority = '';

    /**
     * Create new instance
     * @since 1.1.0
     * @param string $ID
     * @param string $title
     * @param string $description Meta box description that appear
     * @param array  $post_types
     * @param string $context
     * @param string $priority
     * @return type
     */
    public function __construct($ID, $title, $description = '', $post_types = array('post'), $context = 'advanced', $priority = 'default') {

        // Check mandatory values first
        if ($ID == '' || $context == '' || $priority == '') {
            return;
        }

        // Replace title with camelcase slug if not specified
        if ($title == '') {
            $this->title = ucfirst($ID);
        }

        // Allowed post types has to be specified
        if (empty($post_types)) {
            return;
        }

        $this->title = $title;
        $this->description = $description;
        $this->ID = $ID;
        $this->post_types = $post_types;
        $this->meta_key = $this->ID;
        $this->context = $context;
        $this->priority = $priority;

        // https://developer.wordpress.org/reference/hooks/add_meta_boxes/
        add_action('add_meta_boxes', array($this, 'register'));

        // https://developer.wordpress.org/reference/hooks/save_post/
        // add_action('save_post', array($this, 'save_meta_values'), 10, 2);
        
        // https://developer.wordpress.org/reference/hooks/edit_form_after_title/
        add_action('edit_form_after_title', array($this, 'move_after_title'));
    }

    /**
     * Add meta box if current post type is in allowed post types
     * 
     * Calling WordPress function add_meta_box
     * https://developer.wordpress.org/reference/functions/add_meta_box/
     * 
     * @since 1.1.0
     * @param string $post_type Current post type
     */
    public function register($post_type) {
        if (in_array($post_type, $this->post_types)) {
            add_meta_box($this->ID, $this->title, array($this, 'render'), $post_type, $this->context, $this->priority);
        }
    }
    
    /**
     * Move some meta boxes directly after title (top)
     * 
     * Calling WordPress function do_meta_boxes
     * https://developer.wordpress.org/reference/functions/do_meta_boxes/
     * 
     * @since 1.2.0
     */
    public function move_after_title() {
        global $post, $wp_meta_boxes;
        do_meta_boxes(get_current_screen(), 'top', $post);
        unset($wp_meta_boxes[get_post_type($post)]['top']);
    }

    /**
     * Adding fields 
     * 
     * Arguments array specification:
     *  array(
     *      'name' => '',
     *      'title' => '',
     *      'default' => '',
     *      'required' => false,
     *      'format' => '',
     *      'placeholder' => '',
     *      'type' => 'text',
     *      'options' => array(),
     *      'desc' => '',
     *  );
     * 
     * @since 1.1.0
     * @param array $args options for the field to add
     */
    public function add_field($args) {

        // Add allowed fields here
        $allowed_field_types = array(
            'text',
            'textarea',
            'wpeditor',
            'select',
            'radio',
            'checkbox',
            'date',
            'email',
            'tel'
        );

        // If a type is set that is now allowed, don't add the field
        if (isset($args['type']) && $args['type'] != '' && !in_array($args['type'], $allowed_field_types)) {
            return;
        }

        // Load defaults
        $defaults = array(
            'name' => '',
            'title' => '',
            'default' => '',
            'required' => false,
            'placeholder' => '',
            'type' => 'text',
            'options' => array(),
            'desc' => '',
        );
        $args = array_merge($defaults, $args);

        // Name is mandatory
        if ($args['name'] == '') {
            return;
        }

        // Check if field alreayd exists
        foreach ($this->fields as $field) {
            if (isset($field[$args['name']])) {
                trigger_error(printf(__('There is already a field with name %s', 'wetory-support)', $args['name'])));
                return;
            }
        }

        // If there are options set, then use the first option as a default value
        if (!empty($args['options']) && $args['default'] == '') {
            $array_keys = array_keys($args['options']);
            $args['default'] = $array_keys[0];
        }

        // Add field to prive member array
        $this->fields[$args['name']] = $args;
    }

    /**
     * Render meta box with its values 
     * 
     * Uses renderer class function render_fields to render particular fields.
     * 
     * @since 1.1.0
     * @see Wetory_Support_Metabox_Renderer::render_fields($fields)
     * @param WP_POST $post the post object.
     */
    public function render($post) {
        $this->post_id = $post->ID;
        $this->init_meta_values();

        wp_nonce_field('metabox_' . $this->ID, 'metabox_' . $this->ID . '_nonce');
        if ($this->description !== '') {
            echo '<p>' . $this->description . '</p>';
        }
        Metabox_Renderer::render_fields($this->fields);
    }

    /**
     * Load meta values from database if exist
     *
     * @since 1.1.0
     */
    public function init_meta_values() {
        // Iterate fields and retrieve values
        foreach ($this->fields as $name => $field) {

            $this->meta_values[$name] = get_post_meta($this->post_id, $name, true);

            if (isset($this->meta_values[$name])) {
                if ($field['type'] == 'date' && $this->meta_values[$name] !== '') {
                    $this->fields[$name]['default'] = date('d.m.Y', strtotime($this->meta_values[$name]));
                } else {
                    $this->fields[$name]['default'] = $this->meta_values[$name];
                }
            }
        }
    }

    /**
     * Check, sanitize and save post meta values from meta boxes if any. 
     * 
     * Implementation is always sub-class specific.
     * 
     * https://developer.wordpress.org/reference/functions/update_post_meta/
     * 
     * @since    1.1.0
     * 
     * @param int $post_id post ID
     */
    public function save_meta() {
        $this->posted_data = $_POST;

        // Load values if not loaded yet
        if (empty($this->meta_values)) {
            $this->init_meta_values();
        }

        // Iterate fields sanitize and persis value
        foreach ($this->fields as $name => $field) {
            $this->meta_values[$name] = $this->{ 'sanitize_' . $field['type'] }($name);
            
            // Save meta value only if some value specified to save database space
            if(isset($this->meta_values[$name]) && $this->meta_values[$name] !== ''){
                update_post_meta($this->post_id, $name, $this->meta_values[$name]);
            } else {
                delete_post_meta($this->post_id, $name);
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
    public function save_meta_values($post_id, $post) {

        // Verify that the nonce is valid.
        if (!isset($_POST['metabox_' . $this->ID . '_nonce']) || !wp_verify_nonce($_POST['metabox_' . $this->ID . '_nonce'], 'metabox_' . $this->ID)) {
            return $post_id;
        }

        // Check if meta save is allowed
        if (!$this->save_meta_values_allowed($post)) {
            return $post_id;
        }

        // Check if user has required permissions
        if (!$this->check_capabilities($post)) {
            return $post_id;
        }

        $this->post_id = $post_id;
        $this->save_meta();
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
    private function save_meta_values_allowed($post) {
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
     * Gets meta value, using defaults if necessary to prevent undefined notices.
     * 
     * @since 1.1.0
     * @param  string $key
     * @param  mixed  $empty_value
     * @return mixed  The value specified for the meta value or a default value for the option.
     */
    public function get_meta_value($key, $empty_value = null) {

        // Init if no meta values
        if (empty($this->meta_values)) {
            $this->init_meta_values();
        }

        // Get option default if unset.
        if (!isset($this->meta_values[$key])) {
            if (isset($this->fields[$key])) {
                $this->meta_values[$key] = isset($this->fields[$key]['default']) ? $this->fields[$key]['default'] : '';
            }
        }
        if (!is_null($empty_value) && empty($this->meta_values[$key]) && '' === $this->meta_values[$key]) {
            $this->meta_values[$key] = $empty_value;
        }
        return $this->meta_values[$key];
    }

    /**
     * Sanitize text field
     * @param  string $key name of the field
     * @return string     
     */
    public function sanitize_text($key) {
        $text = $this->get_meta_value($key);
        if (isset($this->posted_data[$key])) {
            $text = wp_kses_post(trim(stripslashes($this->posted_data[$key])));
        }
        return $text;
    }

    /**
     * Sanitize date field
     * @param  string $key name of the field
     * @return string Formated date    
     */
    public function sanitize_date($key) {
        $date = $this->get_meta_value($key);
        if (isset($this->posted_data[$key]) && $this->posted_data[$key] !== '') {
            $date_string = sanitize_text_field($this->posted_data[$key]);
            $date = date('Y-m-d', strtotime($date_string));
        }
        return $date;
    }

    /**
     * Sanitize textarea field
     * @param  string $key name of the field
     * @return string      
     */
    public function sanitize_textarea($key) {
        $text = $this->get_meta_value($key);

        if (isset($this->posted_data[$key])) {
            $text = wp_kses(trim(stripslashes($this->posted_data[$key])),
                    array_merge(
                            array(
                                'iframe' => array('src' => true, 'style' => true, 'id' => true, 'class' => true)
                            ),
                            wp_kses_allowed_html('post')
                    )
            );
        }
        return $text;
    }

    /**
     * Sanitize WPEditor field
     * @param  string $key name of the field
     * @return string      
     */
    public function sanitize_wpeditor($key) {
        $text = $this->get_meta_value($key);

        if (isset($this->posted_data[$key])) {
            $text = wp_kses(trim(stripslashes($this->posted_data[$key])),
                    array_merge(
                            array(
                                'iframe' => array('src' => true, 'style' => true, 'id' => true, 'class' => true)
                            ),
                            wp_kses_allowed_html('post')
                    )
            );
        }
        return $text;
    }

    /**
     * Sanitize select field
     * @param  string $key name of the field
     * @return string      
     */
    public function sanitize_select($key) {
        $value = $this->get_meta_value($key);
        if (isset($this->posted_data[$key])) {
            $value = stripslashes($this->posted_data[$key]);
        }
        return $value;
    }

    /**
     * Sanitize radio
     * @param  string $key name of the field
     * @return string      
     */
    public function sanitize_radio($key) {
        $value = $this->get_meta_value($key);
        if (isset($this->posted_data[$key])) {
            $value = stripslashes($this->posted_data[$key]);
        }
        return $value;
    }

    /**
     * Sanitize checkbox field
     * @param  string $key name of the field
     * @return string      
     */
    public function sanitize_checkbox($key) {
        $status = '';
        if (isset($this->posted_data[$key]) && ( 1 == $this->posted_data[$key] )) {
            $status = '1';
        }
        return $status;
    }

}
