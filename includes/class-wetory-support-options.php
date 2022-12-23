<?php

/**
 * Class managing plugin options
 *
 * Loads options using specified functions, which make it easy to get plugin related options.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Options {

    /**
     * Private member to hold key names in plugin options. Just to make it simple
     * if some changes in keys needed.
     * @var type 
     */
    const KEY_SHORTCODES = 'wetory-support-shortcodes';
    const KEY_WIDGETS = 'wetory-support-widgets';
    const KEY_APIKEYS = 'wetory-support-apikeys';
    const KEY_CPT = 'wetory-support-cpt';

    /**
     * Helper function for retrieving settings value from options. 
     * 
     * Plugin options are designed to be stored in sections serialized to
     * one option entry in database. This requries "special" approach to retrieve 
     * particular setting value.
     * 
     * Default values for arguments are used in case nothing is provided.
     * 
     * Example of input
     * 
     * $args = array(
     *      'option_name'      => 'my-option-name',
     *      'option_section'   => 'display',
     *      'option_key'       => 'my-key',
     *      'name'             => 'my-setting'
     * }
     * 
     * @since      1.2.1
     * @param array $args Arguments
     * @return mixed Value of the setting stored in given option and section. A value of any type may be returned, including scalar (string, boolean, float, integer), null, array, object.
     */
    public static function get_settings_value(array $args, mixed $default = null){
        $value = null;
        $defaults = array(
            'option_name' => WETORY_SUPPORT_SETTINGS_OPTION,
            'option_section' => 'general',
            'option_key' => null,
            'name' => null
        );
        $args = wp_parse_args($args, $defaults);

        $section = self::get_settings_section($args);

        if($section && isset($args['name'])){
            $option_key = isset($args['option_key']) && isset($section[$args['option_key']]) ? $section[$args['option_key']] : null;
            if (isset($option_key)) {
                $value = isset($option_key[$args['name']]) ? $option_key[$args['name']] : null;
            } else {
                $value = isset($section[$args['name']]) ? $section[$args['name']] : null;
            }
            $value = isset($value) ? $value : $default;
            return $value;
        }

        return $default;
    }

    /**
     * Helper function for retrieving settings option section. 
     * 
     * Plugin options are designed to be stored in sections serialized to
     * one option entry in database. This requries "special" approach to retrieve 
     * particular section
     * 
     * Default values for arguments are used in case nothing is provided.
     * 
     * Example of input
     * 
     * $args = array(
     *      'option_name'      => 'my-option-name',
     *      'option_section'   => 'display'
     * }
     * 
     * @since      1.2.1
     * @param array $args Arguments
     * @return array Associative array with deserialized options section data
     */
    public static function get_settings_section(array $args){
        $defaults = array(
            'option_name' => WETORY_SUPPORT_SETTINGS_OPTION,
            'option_section' => 'general'
        );
        $args = wp_parse_args($args, $defaults);        

        $option = self::get_settings_option($args['option_name']);

        $section = isset($option[$args['option_section']]) ? $option[$args['option_section']] : false;

        return $section;
    }

    /**
     * Helper function for retrieving plugin settings option. 
     * 
     * By default reading option with name using constant WETORY_SUPPORT_SETTINGS_OPTION 
     * 
     * @since      1.2.1
     * @param string $option_name Name of the option registered for plugin
     * @return array Associative array with deserialized plugin options data
     */
    public static function get_settings_option(string $option_name = WETORY_SUPPORT_SETTINGS_OPTION) {
        $wetory_support_settings = array();
        $settings_cache_status = self::get_settings_cache_status();
        if($settings_cache_status == 'dirty' || empty($wetory_support_settings)){
            $wetory_support_settings = get_option($option_name);
            self::set_settings_cache_status('clean');
        }
        $wetory_support_settings = apply_filters('wetory_settings_default', $wetory_support_settings);
        $wetory_support_settings = apply_filters('wetory_settings_sanitize', $wetory_support_settings);

        return $wetory_support_settings;
    }

    /**
     * Helper function for retrieving plugin settings cache option. 
     * 
     * Not actually in use yet.
     * 
     * @since      1.2.1
     * @return string Settings cache status can be 'dirty' or 'clean'
     */
    private static function get_settings_cache_status(){
        $cache_option = get_option(WETORY_SUPPORT_SETTINGS_CACHE_OPTION);
        if(!isset($cache_option['status'])){
            self::invalidate_setting_cache();
            $cache_option['status'] = 'dirty';
        }
        return $cache_option['status'];
    }

    /**
     * Helper function for setting plugin settings cache option. 
     * 
     * Not actually in use yet.
     * 
     * @since      1.2.1
     * @param string Settings cache status can be 'dirty' or 'clean'
     * @return void
     */
    private static function set_settings_cache_status(string $status){
        $cache_option = array(
            'status' => $status,
        );
        update_option(WETORY_SUPPORT_SETTINGS_CACHE_OPTION, $cache_option);
    }

    /**
     * Invalidating setting cache. 
     * 
     * Just wrapper function for calling set_settings_cache_status('dirty')
     * 
     * Not actually in use yet.
     * 
     * @since      1.2.1
     * @return void
     */
    public static function invalidate_setting_cache(){
        self::set_settings_cache_status('dirty');
    }

    /**
     * Helper function to check if  plugin settings allow usage of given widget
     * @param string $widget Widget class name
     * @return boolean
     * 
     * @since    1.0.0
     */
    public static function use_widget($widget) {

        $option_widgets = get_option(self::KEY_WIDGETS);
        return isset($option_widgets[strtolower($widget)]['use']) && $option_widgets[strtolower($widget)]['use'] == 'on';
    }

    /**
     * Helper function to check if  plugin settings allow usage of given shortcode
     * @param string $shortcode Shortcode class name
     * @return boolean
     * 
     * @since    1.0.0
     */
    public static function use_shortcode($shortcode) {
        $option_shotcodes = get_option(self::KEY_SHORTCODES);
        return isset($option_shotcodes[strtolower($shortcode)]['use']) && $option_shotcodes[strtolower($shortcode)]['use'] == 'on';
    }
    
    /**
     * Helper function to check if  plugin settings allow usage of given custom post type
     * @param string $cpt Custom post type class name
     * @return boolean
     * 
     * @since    1.1.0
     */
    public static function use_cpt($cpt) {
        $option_cpt = get_option(self::KEY_CPT);
        return isset($option_cpt[strtolower($cpt)]['use']) && $option_cpt[strtolower($cpt)]['use'] == 'on';
    }

    /**
     * Get options for given API key object 
     * 
     * @param string $apikey API key object name
     * 
     * @return array  Array with API key options in key => value structure
     * 
     * @since    1.0.0
     */
    public static function get_apikey_options($apikey) {
        $options = isset(get_option(self::KEY_APIKEYS)[$apikey]) ? get_option(self::KEY_APIKEYS)[$apikey] : false;
        return $options;
    }

    /**
     * Get options for given shortcode object 
     * 
     * @param string $shortcode Shortcode object name
     * 
     * @return array  Array with shortcode options in key => value structure
     * 
     * @since    1.0.0
     */
    public static function get_shortcode_options($shortcode) {
        $options = isset(get_option(self::KEY_SHORTCODES)[$shortcode]) ? get_option(self::KEY_SHORTCODES)[$shortcode] : false;
        return $options;
    }
    
    /**
     * Get options for given widget object 
     * 
     * @param string $widget Widget object name
     * 
     * @return array  Array with widget options in key => value structure
     * 
     * @since    1.0.0
     */
    public static function get_widget_options($widget) {
        $options = isset(get_option(self::KEY_WIDGETS)[$widget]) ? get_option(self::KEY_WIDGETS)[$widget] : false;
        return $options;
    }
    
    /**
     * Get options for given custom post type object 
     * 
     * @param string $cpt Custom post type object name
     * 
     * @return array Array with custom post type options in key => value structure
     * 
     * @since    1.1.0
     */
    public static function get_cpt_options($cpt) {
        $options = isset(get_option(self::KEY_CPT)[$cpt]) ? get_option(self::KEY_CPT)[$cpt] : false;
        return $options;
    }

}
