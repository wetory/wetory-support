<?php

/**
 * Base class for widget classes. 
 *
 * This class must be extended for every widget within plugin it alredy extends
 * required WP_Widget class so required methods will be overridden in sub-class
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/widgets
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

abstract class Wetory_Support_Widget extends WP_Widget {

    use Wetory_Support_Object_File_Trait;

    /**
     * Widget ID.
     *
     * @since    1.0.0
     * @access   public Need to be same as parent attribute
     * @var      string  $id  Hold widget identificator.
     */
    public $id;

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct(string $id, string $name, array $args) {
        /*
         * This is needed to properly initiate Wordpress Widget
         * https://developer.wordpress.org/reference/classes/wp_widget/
         */        
        parent::__construct($id, $name, $this->add_args_classname($args));
        $this->id = $id;
    }
    
    /**
     * Registers widget.
     * 
     * Basically only calling register_widget function with actual class name.
     * https://developer.wordpress.org/reference/functions/register_widget/
     * 
     * @since    1.0.0
     */
    public function register() {
        if ($this->use_widget()) {
            register_widget($this->get_class());
            $this->load_sources();
        }
    }
    
    /**
     * Load some additional sources - need to be overridden
     * 
     * Just optional function which can be overridden in subclass to load some
     * JavaScript or other stuff if needed for widget functionality. 
     */
    protected function load_sources(){
        return;
    }

    /**
     * Instantiate subclass extending this abstract class.
     * 
     * @since    1.0.0
     * @param string $subclass Sub-class name
     * @return \Wetory_Support_Widget New instance of given object
     */
    public static function create_instance(string $subclass): Wetory_Support_Widget {
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
     * Options caller. 
     * 
     * Returns false if no options found in database.
     * 
     * @since    1.0.0
     * @see Plugin_Options::get_shortcode_options($shortcode)
     * @return boolean
     */
    public function get_options() {
        $options = Plugin_Options::get_widget_options($this->id);

        return $options;
    }

    /**
     * Check if widget is configured for use. 
     * 
     * @since    1.0.0
     * @see Plugin_Options::use_widget($widget)
     * @return boolean
     */
    public function use_widget() {
        return Plugin_Options::use_widget($this->id);
    }
    
    /**
     * Add general class name to given arguments array.
     * 
     * https://developer.wordpress.org/reference/functions/wp_register_sidebar_widget/
     * 
     * @since    1.0.0
     * @param array $args Arguments passed to WP_Widget constructor
     * @return array Arguments array with class name added/modified
     */
    private function add_args_classname($args) {
        if (isset($args['classname'])) {
            $args['classname'] .= ' wetory-widget';
        } else {
            $args['classname'] = 'wetory-widget';
        }
        return $args;
    }

}
