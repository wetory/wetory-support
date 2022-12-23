<?php

/**
 * Define the widgets provided by plugin
 *
 * Loads widget objects from files in widgets folder
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

use Wetory_Support_Options as Plugin_Options;
use Wetory_Support_Sanitizer as Sanitizer;

class Wetory_Support_Widgets_Controller extends Wetory_Controller
{

    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/widgets/abstract-wetory-support-widget.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/widgets/widget-wetory-support-*.php';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct()
    {
        parent::__construct();
        add_filter('wetory_settings_sections', array($this, 'settings_section'), 10, 1);        
        add_filter('wetory_settings_sanitize', array($this, 'sanitize_settings'), 10, 1);
    }

    public function get_instance($file): Wetory_Support_Widget
    {
        parent::get_instance($file);
        return Wetory_Support_Widget::create_instance($this->get_class($file));
    }

    protected function base_class(): string
    {
        return self::BASE_CLASS;
    }

    protected function glob_filter(): string
    {
        return self::GLOB_FILTER;
    }

    /**
     * Add settings section.
     * 
     * It is hooked into 'wetory_settings_sections' filter
     * which is used to populate final data for sections.
     * 
     * @param array $sections Associative array that holds data about sections
     * 
     * @since    1.2.1
     */
    public function settings_section($sections)
    {

        $section_name = WETORY_SUPPORT_SETTINGS_WIDGETS_SECTION;

        $section = array(
            'title' => __('Widgets', 'wetory-support'),
            'description' => __('Select <a href="https://wordpress.org/support/article/wordpress-widgets/" target="_blank">widgets</a> you want to use in your website', 'wetory-support'),
            'settings_fields' => array()
        );

        // Loop through all plugin's loaded widgets
        $widgets = $this->get_objects();

        if ($widgets) {

            foreach ($widgets as $widget) {

                $widget_id = $widget->get_id();
                $widget_meta = $widget->get_meta();

                unset($field);
                $field = array(
                    'label' => $widget_meta['name'],
                    'type' => 'checkbox',
                    'option_section' => $section_name,
                    'option_key' => $widget_id,
                    'id' => $widget_id . '-use',
                    'name' => 'use',
                    'link' => $widget_meta['link'],
                    'help' => $widget_meta['description'],
                );

                $section['settings_fields'][] = $field;
            }
        }

        $sections[$section_name] = $section;

        return $sections;
    }

    /**
     * Sanitize settings.
     * 
     * It is hooked into 'wetory_settings_sanitize' filter
     * which is used during read/write of settings.
     * 
     * @param array $settings Associative array representing plugin settings
     * 
     * @since    1.2.1
     */
    public function sanitize_settings($settings){

        $section_name = WETORY_SUPPORT_SETTINGS_WIDGETS_SECTION;
        
        $widgets = $this->get_objects();

        if ($widgets) {
            foreach ($widgets as $widget) {
                $widget_id = $widget->get_id();

                if(isset($settings[$section_name][$widget_id]['use']) && !empty($settings[$section_name][$widget_id]['use'])){
                    $settings[$section_name][$widget_id]['use'] = Sanitizer::sanitize_checkbox($settings[$section_name][$widget_id]['use'], 'on');
                }
            }
        }   
        
        return $settings;
    }
}
