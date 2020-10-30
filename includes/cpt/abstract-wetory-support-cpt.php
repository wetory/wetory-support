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
    public function register(){
        if ($this->use_cpt()) {
            register_post_type($this->id, $this->get_arguments());
        }
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
    public function use_cpt(){
        return Plugin_Options::use_cpt($this->id);
    }

}
