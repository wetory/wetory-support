<?php

/**
 * Base class for controller classes. 
 *
 * This class must be extended for every controller within plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
abstract class Wetory_Controller {

    /**
     * Objects collection
     *
     * @since    1.0.0
     * @access   protected
     * @var      array  $objects  Hold associative array with objects handled by controller
     */
    protected $objects;

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_base();
        $this->load();
    }

    /**
     * Call registration method on all objects
     * 
     * @since    1.0.0
     */
    public function register(){
        foreach ($this->objects as $object) {
            $object->register();
        }
    }

    /**
     * Returns path to base class file
     * 
     * @since    1.0.0
     */
    protected abstract function base_class(): string;

    /**
     * Returns filer used in glob function for dynamically loading class files
     * 
     * This method is used in get_files() method of this class.
     * @since    1.0.0
     */
    protected abstract function glob_filter(): string;

    /**
     * Load base class for objects in given folder.
     * 
     * Base class is usually abstract and define base properties for objects files
     * There is one base abstract class which is providing standard functions that
     * can be overridden if needed. 
     * 
     * It is important that all object files extend this base class.
     * 
     * @since    1.0.0
     */
    protected function load_base() {
        $base_class_file = $this->base_class();
        if (file_exists($base_class_file)) {
            require_once $base_class_file;
        }
    }

    /**
     * Load all objects from GLOB_FILTER. 
     * 
     * Naming convention based approach. Function just load array objects
     * into array then will use them in another function.
     * 
     * Example of naming convention that need to be followed:
     *  - file  api-key-wetory-support-google-maps.php
     *  - class Api_Key_Wetory_Support_Google_Maps
     * 
     * There are more folders containing object files. Every folder is handled by 
     * separate controller which extends this class.
     * 
     * @since    1.0.0
     */
    protected function load() {
        $this->objects = array();
        foreach ($this->get_files() as $class_file) {
            array_push($this->objects, $this->get_instance($class_file));
        }
    }

    
    /**
     * Requires given class file.
     * 
     * Some easy implementation of "autoload" process. This method should be overridden
     * and called from overridden method body parent::get_instance($class_file)
     * 
     * @since    1.0.0
     */
    protected function get_instance($class_file) {
        if (file_exists($class_file)) {
            require_once $class_file;
        }
    }

    /**
     * Public access function to private member name
     * 
     * @since    1.0.0
     * @return array
     */
    public function get_objects(): array {
        return $this->objects;
    }

    /**
     * Using glob function to retrieve array of class files. 
     * 
     * Based on glob_filter() result.
     * 
     * https://www.php.net/manual/en/function.glob.php
     * 
     * @since    1.0.0
     * @return array
     * 
     */
    protected function get_files(): array {
        return glob($this->glob_filter());
    }

    /**
     * Returns class name from file name. 
     * 
     * For this funcitonality working all class files need to meet naming convention
     * 
     * Example of naming convention that need to be followed:
     *  - file  api-key-wetory-support-google-maps.php
     *  - class Api_Key_Wetory_Support_Google_Maps
     * 
     * @since    1.0.0
     * 
     * @param type $file
     * @return string
     */
    protected function get_class($file): string {
        return str_replace('-', '_', ucwords(pathinfo($file)['filename'], '-'));
    }

}
