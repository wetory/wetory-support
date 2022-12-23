<?php
/**
 * Validator class.
 *
 * Static methods can be used to validate data valus.
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Validator {

    /**
     * Check if given string is valid date
     * 
     * @param string $value
     * @return int <p>Returns a timestamp on success, <b><code>FALSE</code></b> otherwise. Previous to PHP 5.1.0, this function would return <i>-1</i> on failure.</p>
     */
    public static function is_date(string $value) {
        $time = strtotime($value);        
        return $time !== false ;
    }
}
