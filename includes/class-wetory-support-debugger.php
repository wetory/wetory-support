<?php

/**
 * Debugger class.
 *
 * Helper abstract class that can be used for debugging.
 *
 * @link       https://www.wetory.eu/
 * @since      1.2.1
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Options;

abstract class Wetory_Support_Debugger
{
    // Verbosity level controls what is written to debug.log
    const VerbosityLevelDisabled = 'disabled';
    const VerbosityLevelBasic = 'basic';
    const VerbosityLevelDetailed = 'detailed';
    // Severity for log messages
    const SeverityInfo = 'info';
    const SeverityWarning = 'warning';
    const SeverityError = 'error';

    /**
     * Helper function to get verbosity levels from class constants
     *
     * @return array Values of VerbosityLevel* named constants
     * @since 1.2.1
     * 
     * @see https://www.php.net/manual/en/function.get-called-class.php
     */
    public static function get_verbosity_levels(): array
    {
        $verbosity_levels = array();
        $reflector = new ReflectionClass(get_called_class());
        $constants = $reflector->getConstants();

        foreach ($constants as $name => $value) {
            if (substr($name, 0, 14) === "VerbosityLevel") {
                $verbosity_levels[] = $value;
            }
        }

        return $verbosity_levels;
    }

    /**
     * Custom error writing to debug.log file.
     * 
     * It is writting to log based on plugin settings for debugging verbosity level.
     * 
     * @since      1.2.1
     * @param mixed $log What you want o write to log
     * @param string $severity You can write to log with severity. It is show at the beginning on log message. By default "info".
     * 
     * @see https://www.php.net/manual/en/function.error-log.php
     */
    public static function write_log($log, $severity = self::SeverityInfo){
        // Get debugging verbosity settings
        $verbosity_setting = Options::get_settings_value(array(
            'option_section'   => 'debugging',
            'name'             => 'verbosity'
        ));

        // Get verbosity level for severity
        $verbosity = self::convert_severity_to_verbosity($severity);

        // Write to log only if settings allow it
        if($verbosity_setting != self::VerbosityLevelDisabled && $verbosity_setting == $verbosity) {
            $prepend = 'Wetory ' . ucwords($severity) . ": ";
            if (is_array($log) || is_object($log)) {
                error_log($prepend);
                error_log(print_r($log, true));
            } else {
                error_log($prepend . $log);
            }
        }       
    }

    /**
     * Helper function to get verbosity level for given severity.
     * 
     * Works as mapping between severities and verbosity levels.
     *
     * @param string $severity Log severity
     * @return string Verbosity level
     * @since 1.2.1
     */
    public static function convert_severity_to_verbosity(string $severity){
        switch($severity) {
            case self::SeverityInfo:
            case self::SeverityWarning: 
                $verbosity = self::VerbosityLevelDetailed;
                break;
            case self::SeverityError: 
                $verbosity = self::VerbosityLevelBasic;
                break;
            default:
                $verbosity = self::VerbosityLevelBasic;

        }
        return $verbosity;
    }

}
