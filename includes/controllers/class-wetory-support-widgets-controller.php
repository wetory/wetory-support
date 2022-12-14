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

class Wetory_Support_Widgets_Controller extends Wetory_Controller {
    
    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/widgets/abstract-wetory-support-widget.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/widgets/widget-wetory-support-*.php';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
        add_filter('wetory_support_settings_sections', array($this, 'settings_section'), 10, 1);
    }
    
    public function get_instance($file): Wetory_Support_Widget {
        parent::get_instance($file);
        return Wetory_Support_Widget::create_instance($this->get_class($file));
    }

    protected function base_class(): string {
        return self::BASE_CLASS;
    }

    protected function glob_filter(): string {
        return self::GLOB_FILTER;
    }

    /**
     * Add settings section.
     * 
     * It is hooked into 'wetory_support_settings_sections' filter
     * which is used to populate final data for sections.
     * 
     * @param array $sections Associative array that holds data about sections
     * 
     * @since    1.2.1
     */
    public function settings_section($sections)
    {

        $section_name = 'widgets';

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

}
