<?php

/**
 * Define settings available for the plugin
 *
 * Main purpose of this class is registering hooks to action 'wetory_support_settings_render_section' which 
 * can be used to render settings sections in views. 
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu> 
 */

class Wetory_Support_Settings
{
    /**
     * Object holding abstraction of plugin
     *
     * @since    1.2.1
     * @access   private
     * @var      Wetory_Support $plugin_obj  Instance of main plugin class
     * @see Wetory_Support
     */
    private $plugin_obj;

    /**
     * Associative array holding plugin settings sections
     *
     * @since    1.2.1
     * @access   private
     * @var      array    $sections Associative array.
     */
    private $sections;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      Wetory_Support    $plugin_obj    Instance of main plugin class
     */
    public function __construct($plugin_obj)
    {
        $this->plugin_obj = $plugin_obj;

        add_action('wetory_support_settings_render_section', array($this, 'render_section'), 10, 1);
    }


    /**
     * Wrapper function for registering everything around plugin settings
     * 
     * @since    1.2.1
     *
     * @return void
     */
    public function init_settings()
    {
        $this->register_sections();
    }

    /**
     * Get particular section data
     *
     * @param [string] $section Name which was used to register section
     * @return [array] Associative array
     * 
     * @since    1.2.1
     */
    public function get_section($name)
    {
        if (isset($this->sections[$name])) {
            return $this->sections[$name];
        } else {
            throw new \Exception(__('Given settings section "' . $name . '" not found!', 'wetory-support'));
        }
    }

    /**
     * Render settings section HTML markup
     *
     * @param [string] $section Name which was used to register section
     * @return [mixed] HTML markup for section if section with given name found
     * 
     * @since    1.2.1
     */
    public function render_section($name)
    {
        $section = $this->get_section($name);        
        Wetory_Support_Settings_Renderer::render_settings_section($section);
    }


    /**
     * Automatically include functions matching naming convention into filter used to compile
     * full set of settings sections.
     * 
     * If new section is needed just simply create new function folowing naming standard
     * 'register_section_*' and it will be autmatically hooked to 'wetory_support_settings_sections'
     * filter.
     * 
     * @see https://developer.wordpress.org/plugins/hooks/filters/
     * @see https://developer.wordpress.org/plugins/hooks/custom-hooks/
     * 
     * @since    1.2.1
     *
     * @return void
     */
    public function register_sections()
    {
        $class_methods = get_class_methods(self::class);

        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 17) === "register_section_") {
                add_filter('wetory_support_settings_sections', array($this, $method_name), 10, 1);
            }
        }

        $this->sections = array();
        $this->sections = apply_filters('wetory_support_settings_sections', $this->sections);
    }
}
