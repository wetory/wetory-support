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
    }
    
    public function get_instance($file): Wetory_Support_Widget {
        parent::get_instance($file);
        return Wetory_Support_Widget::create_instance($this->get_class($file));
    }
    
    public function register() {
        foreach ($this->objects as $widget) {
            $widget->register();
        }
    }

    protected function base_class(): string {
        return self::BASE_CLASS;
    }

    protected function glob_filter(): string {
        return self::GLOB_FILTER;
    }

}
