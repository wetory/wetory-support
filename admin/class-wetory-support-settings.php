<?php

/**
 * Define settings available for the plugin
 *
 * Main purpose of this class is registering hooks to action 'wetory_settings_render_section' which 
 * can be used to render settings sections in views. 
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu> 
 */

 use Wetory_Support_Sanitizer as Sanitizer;

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

        add_action('wetory_settings_render_section', array($this, 'render_section'), 10, 1);

        add_filter('wetory_settings_default', array($this, 'get_default_settings'), 10, 1);
        add_filter('wetory_settings_validate', array($this, 'validate_settings'), 10, 1);
        add_filter('wetory_settings_sanitize', array($this, 'sanitize_settings'), 10, 1);
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
            throw new Exception(sprintf(__('Given settings section "%s" not found!', 'wetory-support'), $name));
        }
    }

    /**
     * Render settings section HTML markup
     *
     * @param [string] $name Name which was used to register section
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
     * 'register_section_*' and it will be autmatically hooked to 'wetory_settings_sections'
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
                add_filter('wetory_settings_sections', array($this, $method_name), 10, 1);
            }
        }

        $this->sections = array();
        $this->sections = apply_filters('wetory_settings_sections', $this->sections);
    }


    public function register_section__debugging($sections)
    {

        $section_name = 'debugging';

        $section = array(
            'title' => __('Debugging', 'wetory-support'),
            'description' => __('Here you can modify plugin debugging behaviour.', 'wetory-support'),
            'settings_fields' => array()
        );

        $field = array(
            'label' => __('Verbosity', 'wetory-support'),
            'type' => 'select',
            'option_section' => $section_name,
            'id' => 'verbosity',
            'name' => 'verbosity',
            'required'       => true,
            'options'        => array(
                'disabled' => __('Disabled', 'wetory-support'),
                'basic'  =>  __('Basic', 'wetory-support'),
                'detailed'  =>  __('Detailed', 'wetory-support')
            ),
            'description' => __('Debugging is working only if <a href="https://wordpress.org/support/article/debugging-in-wordpress/" target="_blank">WordPress debugging</a> is enabled.', 'wetory-support'),
            'help' =>  __('Verbosity level can be changed to control what is written to WordPress debug log.', 'wetory-support'),
        );

        $section['settings_fields'][] = $field;

        $sections[$section_name] = $section;

        return $sections;
    }

    /**
     * Validate data passed to plugin settings
     * 
     * This function is hooked to 'wetory_settings_validate' filter.
     * 
     * @see https://developer.wordpress.org/plugins/hooks/filters/
     * @see https://developer.wordpress.org/plugins/hooks/custom-hooks/
     * 
     * @param array $settings Associative array representing plugin settings before validation
     * @return array Associative array representing validated plugin settings
     * 
     * @since    1.2.1
     */
    public function validate_settings($settings)
    {
        wetory_write_log(__('Validating plugin settings', 'wetory-support'));
        
        $wp_error = new WP_Error();

        // Doing sanitization for now - may be extended to the future
        $settings = apply_filters('wetory_settings_sanitize', $settings);

        // Debugging section
        if(isset($settings['debugging']['verbosity']) && $settings['debugging']['verbosity'] ===''){
            $wp_error->add(500, __('Value for debugging verbosity is not valid!','wetory-support'));
        }

        // Return error instead of settings array
        if($wp_error->has_errors()) {
            return $wp_error;
        }

        return $settings;
    }

    /**
     * Sanitize plugin settings data after retrieving from database
     * 
     * This function is hooked to 'wetory_settings_sanitize' filter.
     * 
     * @see https://developer.wordpress.org/plugins/hooks/filters/
     * @see https://developer.wordpress.org/plugins/hooks/custom-hooks/
     * 
     * @param array $settings Associative array representing plugin settings retrieved from database
     * @return array Associative array representing sanitized plugin settings
     * 
     * @since    1.2.1
     */
    public function sanitize_settings($settings)
    {
        // Maintenance section
        if(isset($settings['maintenance']['maintenance-page']['disable-autorecreate']) && !empty($settings['maintenance']['maintenance-page']['disable-autorecreate'])){
            $settings['maintenance']['maintenance-page']['disable-autorecreate'] = Sanitizer::sanitize_checkbox($settings['maintenance']['maintenance-page']['disable-autorecreate'], 'on');
        }

        // Debugging section
        if(isset($settings['debugging']['verbosity']) && !empty($settings['debugging']['verbosity'])){
            $settings['debugging']['verbosity'] = Sanitizer::sanitize_select($settings['debugging']['verbosity'], array('disabled', 'basic', 'detailed'));
        }

        return $settings;
    }

    /**
     * Get defaults for plugin settings
     * 
     * This function is hooked to 'wetory_settings_default' filter.
     * 
     * @see https://developer.wordpress.org/plugins/hooks/filters/
     * @see https://developer.wordpress.org/plugins/hooks/custom-hooks/
     * 
     * @param array $settings Associative array representing plugin setting
     * @return array Associative array representing plugin settings enriched with default values if not presented
     * 
     * @since    1.2.1
     */
    public function get_default_settings($settings)
    {
        $deafult_settings = array(
            'debugging' => array(
                'verbosity' => 'disabled',
            )

        );

        $settings = wp_parse_args($settings, $deafult_settings);

        return $settings;
    }
}
