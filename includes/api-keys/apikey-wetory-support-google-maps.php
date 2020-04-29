<?php

/**
 * Name: Google Maps
 * Description: API keys required for communication with Goole Maps API service
 * 
 * Link: https://cloud.google.com/maps-platform/maps
 *
 * @since      1.0.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/api-keys
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

class Apikey_Wetory_Support_Google_Maps extends Wetory_Support_Apikey {

    /**
     * Create new instance with static properties
     * 
     * @since    1.0.0
     */
    public function __construct() {
        // specify API requirements here
        $name = 'google-maps';
        $keys = array('api-key');
        $fields = array();
        parent::__construct($name, $keys, $fields);
    }
    
    /**
     * Registers Google Maps API Key.
     * 
     * Basically only adding filters with member functions callbacks.
     * 
     * @since    1.0.0
     */
    public function register() {
        $options = $this->get_options();
        if ($options) {
            add_filter('clean_url', array($this, 'add_keys_to_url'), 99, 3);
            add_filter('acf/fields/google_map/api', array($this, 'acf_google_map_api'));
        }
    }

    /**
     * Modifies given URL by adding required keys to it
     * 
     * This function is prepared to be passed as callback to 'clean_url' hook
     *
     * https://developer.wordpress.org/reference/hooks/clean_url/
     *
     * @since 1.1.0
     *
     * @param string $url          URL.
     * @param string $original_url Original URL.
     * @param string $_context     Context.
     */
    public function add_keys_to_url($url, $original_url, $_context) {

        $apikey_settings = $this->get_options();

        if (!isset($apikey_settings['api-key'])) {
            return $url;
        }

        if (strstr($url, "maps.google.com/maps/api/js") !== false || strstr($url, "maps.googleapis.com/maps/api/js") !== false) {

            if (strstr($url, "key=") === false) {
                $url = add_query_arg('key', $apikey_settings['api-key'], $url);
                $url = str_replace("&#038;", "&amp;", $url);
            }
        }

        return $url;
    }

    /**
     * Registering Google API Key autmatically for ACF. Based on article on ACF blog
     * 
     * https://www.advancedcustomfields.com/blog/google-maps-api-settings/
     * 
     * @since    1.0.0
     * @param array $api
     * @return type
     */
    public function acf_google_map_api($api) {
        $apikey_settings = $this->get_options();
        $api['key'] = $apikey_settings['api-key'];

        return $api;
    }
}
