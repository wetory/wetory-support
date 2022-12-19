<?php

/**
 * Define the shortcodes provided by plugin
 *
 * Loads shortcode objects from files in shortcodes folder. File need to meet
 * naming convention and contain shortcode class.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

class Wetory_Support_Shortcodes_Controller extends Wetory_Controller
{

    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/shortcodes/abstract-wetory-support-shortcode.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/shortcodes/shortcode-wetory-support-*.php';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct()
    {
        parent::__construct();
        add_filter('wetory_settings_sections', array($this, 'settings_section'), 10, 1);
    }

    public function get_instance($file): Wetory_Support_Shortcode
    {
        parent::get_instance($file);
        return Wetory_Support_Shortcode::create_instance($this->get_class($file));
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
        $section_name = 'shortcodes';

        $section = array(
            'title' => __('Shortcodes', 'wetory-support'),
            'description' => __('Select <a href="https://codex.wordpress.org/Shortcode/" target="_blank">shortcodes</a> you want to use in your website', 'wetory-support'),
            'settings_fields' => array()
        );

        // Loop through all plugin's loaded shortcodes
        $shortcodes = $this->get_objects();

        if ($shortcodes) {

            foreach ($shortcodes as $shortcode) {

                $shortcode_markup = $shortcode->get_shortcode();
                $shortcode_id = $shortcode->get_id();
                $shortcode_meta = $shortcode->get_meta();

                unset($field);
                $field = array(
                    'label' => $shortcode_meta['name'] . ' ' . $shortcode_markup,
                    'type' => 'checkbox',
                    'option_section' => $section_name,
                    'option_key' => $shortcode_id,
                    'id' => $shortcode_id . '-use',
                    'name' => 'use',
                    'link' => $shortcode_meta['link'],
                    'help' => $shortcode_meta['description'],
                );

                $section['settings_fields'][] = $field;
            }
        }

        $sections[$section_name] = $section;

        return $sections;
    }
}
