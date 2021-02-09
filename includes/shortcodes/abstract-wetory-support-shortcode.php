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

    use Wetory_Support_Object_File_Trait;

    /**
     * Shortcode ID.
     *
     * @since    1.0.0
     * @access   private
     * @var      string  $id  Hold shortcode identificator.
     */
    private $id;

    /**
     * Supported settings for shortcode.
     * 
     * It is used by unpacking passed attributes and applying defaults in shortcode_atts
     * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
     * @access private
     * @var array $supported_atts Array with pairs
     */
    private $shortcode_settings;

    /**
     * String to be added at the beginning of rendered content.
     * 
     * Can be useful to wrap content into sections/containers etc.
     *
     * @since    1.1.0
     * @access   protected
     * @var      string $before_content Content beginning 
     */
    protected $before_content = '';
    
    /**
     * String to be added at the end of rendered content.
     * 
     * Can be useful to wrap content into sections/containers etc.
     *
     * @since    1.1.0
     * @access   protected
     * @var      string $before_content Content ending 
     */
    protected $after_content = '';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct(string $id, array $settings = array()) {
        $this->id = $id;
        $this->shortcode_settings = $settings;
    }
    
    /**
     * Registers shortcode object
     * 
     * Basically only calling add_shortcode function with member functions callbacks.
     * https://developer.wordpress.org/reference/functions/add_shortcode/
     * 
     * @since    1.0.0
     */
    public function register() {
        if ($this->use_shortcode()) {
            $this->load_sources();
            add_shortcode($this->id, array($this, 'render_shortcode'));
        }
    }

    /**
     * Construct shortcode content
     * 
     * Implementation is always sub-class specific.
     * 
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     */
    public abstract function get_content($atts, $content = "");

    /**
     * Render shortcode based on sub-class implementation
     * 
     * @since 1.1.0
     *
     * @param array $atts     Array of attributes
     * @param array $content  Shortcode content or null if not set.
     */
    public function render_shortcode($atts, $content = ""){
        $_atts = $this->parse_attributes($atts);
        $shortcode = $this->before_content;
        $shortcode.= $this->get_content($_atts, $content);
        $shortcode.= $this->after_content;
        return $shortcode;
    }
    
    /**
     * Load some additional sources - need to be overridden
     * 
     * Just optional function which can be overridden in subclass to load some
     * JavaScript or other stuff if needed for widget functionality. 
     * 
     * @since    1.1.0
     */
    protected function load_sources(){
        return;
    }

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
    public function use_shortcode() {
        return Plugin_Options::use_shortcode($this->id);
    }

    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     * 
     * @param array|string $atts User defined attributes in shortcode tag
     * @return array Combined and filtered attribute list.
     */
    protected function parse_attributes($atts): array {
        return shortcode_atts($this->shortcode_settings, $atts, $this->id);
    }

}
