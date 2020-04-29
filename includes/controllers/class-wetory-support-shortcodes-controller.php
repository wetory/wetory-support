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

class Wetory_Support_Shortcodes_Controller extends Wetory_Controller{
    
    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/shortcodes/abstract-wetory-support-shortcode.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/shortcodes/shortcode-wetory-support-*.php';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function get_instance($file): Wetory_Support_Shortcode {
        parent::get_instance($file);
        return Wetory_Support_Shortcode::create_instance($this->get_class($file));
    }
    
    public function register() {
        foreach ($this->objects as $shortcode) {
            $shortcode->register();
        }
    }

    protected function base_class(): string {
        return self::BASE_CLASS;
    }

    protected function glob_filter(): string {
        return self::GLOB_FILTER;
    }

}
