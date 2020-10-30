<?php

/**
 * Define the custom post types provided by plugin
 *
 * Loads custom post type objects from files in cpt folder. File need to meet
 * naming convention and contain custom post type class.
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

class Wetory_Support_Cpt_Controller extends Wetory_Controller{
    
    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/cpt/abstract-wetory-support-cpt.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/cpt/cpt-wetory-support-*.php';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function get_instance($file): Wetory_Support_Cpt {
        parent::get_instance($file);
        return Wetory_Support_Cpt::create_instance($this->get_class($file));
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
