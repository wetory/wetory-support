<?php

/**
 * Sanitizer class.
 *
 * Static methods can be used to sanitize data values.
 *
 * @link       https://www.wetory.eu/
 * @since      1.2.1
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Sanitizer
{


    /**
     * Sanitize checkbox
     *
     * @param int|string|bool $value Value to be sanitized
     * @param int|string|bool string $expected_value The expected value
     * @return int|string|bool Sanitized value of the checkbox.
     * 
     * @since 1.2.1
     */
    public static function sanitize_checkbox($value, $expected_value)
    {
        if ($expected_value == $value) {
            return $expected_value;
        } else {
            return '';
        }
    }

    /**
     * Sanitize slug
     *
     * @param string $value Value to be sanitized
     * @return string Sanitized slug.
     * 
     * @see https://developer.wordpress.org/reference/functions/sanitize_title/
     * 
     * @since 1.2.1
     */
    public static function sanitize_slug($value)
    {
        return sanitize_title($value);
    }

    /**
     * Sanitize verbosity setting for debugging
     *
     * @param string|int $value Value to be sanitized
     * @param array $valid_values Array with all possible valid  values for select
     * @return string|int Sanitized value.
     * 
     * @see https://developer.wordpress.org/reference/functions/sanitize_title/
     * 
     * @since 1.2.1
     */
    public static function sanitize_select($value, $valid_values)
    {
        $value = sanitize_text_field($value);
        if (!in_array($value, $valid_values)) {
            $value = '';
        }
        return $value;
    }
}
