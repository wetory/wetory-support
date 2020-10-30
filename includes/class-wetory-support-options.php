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
    const KEY_LIBRARIES = 'wetory-support-libraries';
    const KEY_APIKEYS = 'wetory-support-apikeys';
    const KEY_CPTS = 'wetory-support-cpts';

    /**
     * Helper function to check if  plugin settings allow usage of given library. 
     * Every library can be used in front end or admin area. 
     * 
     * @param string $library Library name
     * @param string $area Website area where library is set to use in. Can be 'admin' or 'public'. Default value is 'public'
     * @return boolean
     * 
     * @since    1.0.0
     */
    public static function use_library($library, $area = 'public') {

        $option_libraries = get_option(self::KEY_LIBRARIES);
        return isset($option_libraries[strtolower($library)]['use-' . $area]) && $option_libraries[strtolower($library)]['use-' . $area] == 'on';
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
        $option_cpts = get_option(self::KEY_CPTS);
        return isset($option_cpts[strtolower($cpt)]['use']) && $option_cpts[strtolower($cpt)]['use'] == 'on';
    }

    /**
     * Get options for given library 
     * 
     * @param string $library Library name
     * 
     * @return array  Array with library options in key => value structure
     * 
     * @since    1.0.0
     */
    public static function get_library_options($library) {
        $options = get_option(self::KEY_LIBRARIES)[$library];
        return $options;
    }
    
    /**
     * Update options stored in database for given library
     * @param string $library Library name
     * @param array $library_options Library options associative array key => value
     */
    public static function update_library_options(string $library, array $library_options) {
        $options = get_option(self::KEY_LIBRARIES);
        foreach ($library_options as $key => $value) {
            if($options && isset($options[$library])) {
                $options[$library][$key] = $value;
            }            
        }
        update_option(self::KEY_LIBRARIES, $options);
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
        $options = get_option(self::KEY_APIKEYS)[$apikey];
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
        $options = isset(get_option(self::KEY_CPTS)[$cpt]) ? get_option(self::KEY_CPTS)[$cpt] : false;
        return $options;
    }

}
