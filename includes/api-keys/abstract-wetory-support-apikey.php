<?php

/**
 * Base class for API key classes. 
 *
 * This class must be extended for every API key within plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/api-keys
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

abstract class Wetory_Support_Apikey {
    
    use Wetory_Support_Object_File_Trait;

    /**
     * API key ID.
     *
     * @since    1.0.0
     * @access   private
     * @var      string  $id  Hold API key identificator.
     */
    private $id;

    /**
     * API keys.
     *
     * @since    1.0.0
     * @access   private
     * @var      array  $keys  Hold keys array for API.
     */
    private $keys;

    /**
     * Additional fields if needed. Can differ based on API.
     *
     * @since    1.0.0
     * @access   private
     * @var      array  $fields  Hold keys array for additional fields.
     */
    private $fields;

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct(string $id, array $keys, array $fields) {
        $this->id = $id;
        $this->keys = $keys;
        $this->fields = $fields;
    }
    
    /**
     * Instantiate subclass extending this abstract class.
     * 
     * @since    1.0.0
     * @param string $subclass Sub-class name
     * @return \Wetory_Support_Apikey New instance of given object
     */
    public static function create_instance(string $subclass) : Wetory_Support_Apikey {
        return new $subclass();
    }

    /**
     * Registers API objects. Implementation is always sub-class specific.
     * @since    1.0.0
     */
    public abstract function register();

    /**
     * Public access function to private member id
     * 
     * @since    1.0.0
     * @return string
     */
    public function get_id(): string {
        return $this->id;
    }

    /**
     * Public access function to private member keys. It is special as
     * converting private member keys to associative array where creating label
     * uppercase key and replacing "-" and "_" with spaces.
     * 
     * @since    1.0.0
     * @return array
     */
    public function get_keys(): array {
        $return = array();
        foreach ($this->keys as $key) {
            $return[$key] = strtoupper(str_replace(array('-', '_'), ' ', $key));
        }
        return $return;
    }

    /**
     * Options caller. 
     * 
     * Returns false if no options found in database.
     * 
     * @since    1.0.0
     * @see Plugin_Options::get_apikey_options($apikey)
     * @return boolean
     */
    public function get_options() {
        $options = Plugin_Options::get_apikey_options($this->id);

        foreach ($this->keys as $key) {
            if (!isset($options[$key])) {
                return false;
            }
        }
        return $options;
    }

    /**
     * Public access function to private member fields
     * 
     * @since    1.0.0
     * @return array
     */
    public function get_fields(): array {
        return $this->fields;
    }

}
