<?php

/**
 * Class handling admin notices easily.
 * 
 * I've decided to store notices in options table. Holding them in multidimensional 
 * array per user. Auto cleanup is done after notice is displayed.
 * 
 * @since 1.1.0
 *
 * @link       https://www.wetory.eu/
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Admin_Notices {

    /**
     *  String used as option_name in wp_options table
     * 
     *  Changed in version 1.1.0
     */
    const NOTICES_OPTION = 'wetory-support-admin-notices';

    /**
     * Display notices retrieved from options table. 
     * 
     * Iterating notices for actual user and removing them once when displayed.
     * 
     * @see https://developer.wordpress.org/reference/functions/get_option/
     * @see https://developer.wordpress.org/reference/functions/update_option/
     * @since 1.1.0
     */
    public static function display_notices() {
        $current_user = get_current_user_id();

        $notices = get_option(self::NOTICES_OPTION, array());

        if (isset($notices[$current_user]) && sizeof($notices[$current_user]) > 0) {
            foreach ($notices[$current_user] as $key => $notice) {
                $message = isset($notice['message']) ? $notice['message'] : false;
                $severity = !empty($notice['severity']) ? $notice['severity'] : 'error';

                if ($message) {
                    self::display_notice($message, $severity);
                }

                unset($notices[$current_user][$key]);
            }

            update_option(self::NOTICES_OPTION, $notices);
        }
    }

    /**
     * Add new notice with given message and severity. 
     * 
     * Adding new values into multidimensional array stored in options table.
     * 
     * @see https://developer.wordpress.org/reference/functions/get_option/
     * @see https://developer.wordpress.org/reference/functions/update_option/
     * @since 1.1.0
     * 
     * @param string $message Message to appear in notice.
     * @param string $severity Notice severity, can be one of 'error', 'warning',  'info', 'success'. Default value is 'info'
     */
    private static function add_notice($message, $severity = 'info') {
        $diff_stamp = str_replace('.', '', microtime(true));
        $current_user = get_current_user_id();

        $notices = get_option(self::NOTICES_OPTION, array());

        $notices[$current_user][$diff_stamp] = array(
            'message' => $message,
            'severity' => $severity
        );

        update_option(self::NOTICES_OPTION, $notices);
    }

    /**
     * Display new notice with given message and severity. 
     * 
     * @since 1.1.0
     * 
     * @param string $message Message to appear in notice.
     * @param string $severity Notice severity, can be one of 'error', 'warning',  'info', 'success'. Default value is 'info'
     */
    private static function display_notice($message, $severity = 'info') {
        echo "<div class='notice notice-{$severity} is-dismissible'><p>{$message}</p></div>";
    }

    /**
     * Add notice with severity = 'error'
     * 
     * @since 1.1.0
     * @param string $message Message to appear in notice.
     * @param bool $display Immediately display notice without persisting it, by default false.
     */
    public static function error($message, $display = false) {
        if ($display) {
            self::display_notice($message, 'error');
        } else {
            self::add_notice($message, 'error');
        }
    }

    /**
     * Add notice with severity = 'warning'
     * 
     * @since 1.1.0
     * @param string $message Message to appear in notice.
     * @param bool $display Immediately display notice without persisting it, by default false.
     */
    public static function warning($message, $display = false) {
        if ($display) {
            self::display_notice($message, 'warning');
        } else {
            self::add_notice($message, 'warning');
        }
    }

    /**
     * Add notice with severity = 'info'
     * 
     * @since 1.1.0
     * @param string $message Message to appear in notice.
     * @param bool $display Immediately display notice without persisting it, by default false.
     */
    public static function info($message, $display = false) {
        if ($display) {
            self::display_notice($message, 'info');
        } else {
            self::add_notice($message, 'info');
        }
    }

    /**
     * Add notice with severity = 'success'
     * 
     * @since 1.1.0
     * @param string $message Message to appear in notice.
     * @param bool $display Immediately display notice without persisting it, by default false.
     */
    public static function success($message, $display = false) {
        if ($display) {
            self::display_notice($message, 'success');
        } else {
            self::add_notice($message, 'success');
        }
    }

}
