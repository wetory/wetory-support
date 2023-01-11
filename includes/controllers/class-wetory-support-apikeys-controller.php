<?php

/**
 * Define the API keys provided by plugin
 *
 * Loads API key objects from files in api-keys folder
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

use Wetory_Support_Sanitizer as Sanitizer;

class Wetory_Support_Apikeys_Controller extends Wetory_Controller
{

    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/api-keys/abstract-wetory-support-apikey.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/api-keys/apikey-wetory-support-*.php';

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

    public function get_instance($file): Wetory_Support_Apikey
    {
        parent::get_instance($file);
        return Wetory_Support_Apikey::create_instance($this->get_class($file));
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
        $section_name = WETORY_SUPPORT_SETTINGS_APIKEYS_SECTION;

        $section = array(
            'title' => __('API Keys', 'wetory-support'),
            'description' => __('Configure API keys for APIs you want to use in your website', 'wetory-support'),
            'settings_fields' => array()
        );

        // Loop through all plugin's loaded API key objects
        $apikeys = $this->get_objects();

        if ($apikeys) {

            foreach ($apikeys as $apikey) {

                $apikey_id = $apikey->get_id();
                $apikey_meta = $apikey->get_meta();

                $keys = $apikey->get_keys();
                foreach ($keys as $key => $label) {
                    $label = sizeof($keys) > 1 ? $apikey_meta['name'] . ' - ' . $label : $apikey_meta['name'];
                    unset($field);
                    $field = array(
                        'label' => $label,
                        'type' => 'text',
                        'option_section' => $section_name,
                        'option_key' => $apikey_id,
                        'id' => $apikey_id . '-' . $key,
                        'name' => $key,
                        'link' => $apikey_meta['link'],
                        'help' => $apikey_meta['description'],
                    );

                    $section['settings_fields'][] = $field;
                }
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
    public function sanitize_settings($settings)
    {

        $section_name = WETORY_SUPPORT_SETTINGS_APIKEYS_SECTION;

        $apikeys = $this->get_objects();

        if ($apikeys) {

            foreach ($apikeys as $apikey) {

                $apikey_id = $apikey->get_id();

                $keys = $apikey->get_keys();
                foreach ($keys as $key => $label) {          
                    if (isset($settings[$section_name][$apikey_id][$key])) {
                        if (!empty($settings[$section_name][$apikey_id][$key])) {
                            $settings[$section_name][$apikey_id][$key] = Sanitizer::sanitize_text($settings[$section_name][$apikey_id][$key]);
                        } else {
                            unset($settings[$section_name][$apikey_id][$key]);
                        }
                    }
                }
            }
        }

        return $settings;
    }
}
