<?php

/**
 * Define settings available for the plugin
 *
 * Adding menu item and settings page to administration section. Should be instantiated only for admin
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
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

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
     * Associative array holding plugin pages links
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $links Associative array.
     */
    private $links;

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
        $this->plugin_name = $plugin_obj->get_plugin_name();

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
        if(isset($section['type']) && $section['type'] == 'horizontal_form_table') {
            // Wetory_Support_Settings_Renderer::render_horizontal_form_table($args, $cpt_array_objects);
        } else {
            Wetory_Support_Settings_Renderer::render_settings_section($section);
        }
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

    /**
     * Render custom settings form.
     * 
     * Function rendering custom settings form in form of table where rows are custom post types
     * and columns are their options. This is nicely feasible as all custom post types funcitons
     * are same. Using special render function
     * 
     * @see Wetory_Support_Settings_Renderer::render_horizontal_form_table($args, $data) 
     * 
     * @since    1.1.0
     */
    /*
    private function render_cpt_form_table() {

        $option_name = self::CPT_OPTION;

        // Load custom post type objects
        $cpt_objects = $this->plugin_cpt->get_objects();
        $cpt_array_objects = array();

        // Convert objects to arrays
        foreach ($cpt_objects as $cpt_object) {
            array_push($cpt_array_objects, $cpt_object->to_array());
        }

        if ($cpt_objects) {
            unset($args);
            $args = array(
                'option_name' => $option_name,
                'columns' => array(
                    'name' => array(
                        'label' => __('Post type', 'wetory-support'),
                        'type' => 'raw',
                    ),
                    'id' => array(
                        'label' => __('Post type key', 'wetory-support'),
                        'type' => 'raw',
                    ),
                    'use' => array(
                        'label' => __('Use', 'wetory-support'),
                        'type' => 'checkbox',
                        'help' => __('Check if you want to start using post type.', 'wetory-support'),
                    ),
                    'rewrite-slug' => array(
                        'label' => __('Rewrite slug', 'wetory-support'),
                        'type' => 'text',
                        'help' => __('Customize the permastruct slug. Defaults to post type key.', 'wetory-support'),
                    ),
                    'comments' => array(
                        'label' => __('Comments', 'wetory-support'),
                        'type' => 'checkbox',
                        'help' => __('Check if you want to allow comments for post type.', 'wetory-support'),
                    ),
                    'excerpt' => array(
                        'label' => __('Excerpt', 'wetory-support'),
                        'type' => 'checkbox',
                        'help' => __('Check if you want to allow excerpt for post type.', 'wetory-support'),
                    ),
                    'revisions' => array(
                        'label' => __('Revisions', 'wetory-support'),
                        'type' => 'checkbox',
                        'help' => __('Check if you want to allow revisions for post type.', 'wetory-support'),
                    ),
                    'published-posts' => array(
                        'label' => __('Published posts', 'wetory-support'),
                        'type' => 'raw',
                    ),
                    'description' => array(
                        'label' => '',
                        'type' => 'tooltip',
                        'class' => 'compact',
                    ),
                    'link' => array(
                        'label' => '',
                        'type' => 'link',
                        'source' => 'meta',
                        'class' => 'compact',
                    )
                ),
            );
            Settings_Renderer::render_horizontal_form_table($args, $cpt_array_objects);
        }
    }
    */
}
