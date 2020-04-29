<?php

/**
 * Base class for shortcode classes. 
 *
 * This class must be extended for every shortcode within plugin and function
 * Wetory_Shortcode::render_shortcode must be overridden with shortcode specific code
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/shortcodes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

abstract class Wetory_Support_Shortcode {
    
    use Wetory_Support_Object_File;

    /**
     * Shortcode ID.
     *
     * @since    1.0.0
     * @access   private
     * @var      string  $id  Hold shortcode identificator.
     */
    private $id;

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * Echoes the shortcode content.
     *
     * Sub-classes has to override this function to generate their shortcode code.
     *
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     */
    public abstract function render_shortcode($atts, $content = "");

    /**
     * Registers shortcode objects. Implementation is always sub-class specific.
     * @since    1.0.0
     */
    public abstract function register();

    /**
     * Instantiate subclass extending this abstract class.
     * 
     * @since    1.0.0
     * @param string $subclass Sub-class name
     * @return \Wetory_Support_Shortcode New instance of given object
     */
    public static function create_instance(string $subclass): Wetory_Support_Shortcode {
        return new $subclass();
    }

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
     * Get standardized shortcode markup. 
     * 
     * Returns markup that can be used to use shortcode in WordPress. Basically
     * adding "[" and "]" arround its name.
     * 
     * @since 1.1.0
     */
    public function get_shortcode() {
        return '[' . $this->id . ']';
    }

    /**
     * Options caller. 
     * 
     * Returns false if no options found in database.
     * 
     * @since    1.0.0
     * @see Plugin_Options::get_shortcode_options($shortcode)
     * @return boolean
     */
    public function get_options() {
        $options = Plugin_Options::get_shortcode_options($this->id);
        
        return $options;
    }
    
    /**
     * Check if shortcode is configured for use. 
     * 
     * @since    1.0.0
     * @see Plugin_Options::use_shortcode($shortcode)
     * @return boolean
     */
    public function use_shortcode(){
        return Plugin_Options::use_shortcode($this->id);
    }

}
