<?php

/**
 * Trait with basic object functions. 
 *
 * Every object in this plugin is usually wrapped in class defined in file. This
 * trait is there to help identify those files and provide easy way how to include
 * this functions in any class using this trait.
 * 
 * Idea is to share functions among more classes from one place as it is copy-pasted
 * 
 * https://www.php.net/manual/en/language.oop5.traits.php
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/traits
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

trait Wetory_Support_Object_File_Trait
{
    /**
     * Get actual class name
     * @since    1.0.0
     * @return string
     */
    public function get_class(): string {
        return get_class($this);
    }

    /**
     * Get file name where class is specified. 
     * 
     * Using PHP built-in reflection
     * 
     * @since    1.0.0
     * @see ReflectionClass
     * @return string
     */
    public function get_file(): string {
        $reflector = new ReflectionClass($this->get_class());
        return $reflector->getFileName();
    }

    /**
     * Get server path to file where class is specified. 
     * 
     * @since    1.0.0
     * @return string
     */
    public function get_file_path(): string {
        return dirname($this->get_file());
    }

    /**
     * Read meta information written in file header. Read first 8kb only
     * and meta data are read per line.
     * 
     * https://developer.wordpress.org/reference/functions/get_file_data/
     * 
     * @since    1.0.0
     * @return array Meta data array containing name, description and link to documentation
     */
    public function get_meta() {
        $meta = get_file_data($this->get_file(), array(
            'name' => 'Name',
            'description' => 'Description',
            'link' => 'Link',
        ));
        return $meta;
    }
}
