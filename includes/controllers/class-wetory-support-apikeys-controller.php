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
class Wetory_Support_Apikeys_Controller extends Wetory_Controller {

    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/api-keys/abstract-wetory-support-apikey.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/api-keys/apikey-wetory-support-*.php';
    
    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function get_instance($file): Wetory_Support_Apikey {
        parent::get_instance($file);
        return Wetory_Support_Apikey::create_instance($this->get_class($file));
    }

    protected function base_class(): string {
        return self::BASE_CLASS;
    }

    protected function glob_filter(): string {        
        return self::GLOB_FILTER;
    }

}
