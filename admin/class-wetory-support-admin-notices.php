<?php

/**
 * Class handling admin notices easily.
 *
 * @link       https://www.wetory.eu/
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Admin_Notices {

    const NOTICE_FIELD = 'wetory-support-admin_notice_message';

    public static function display_notice() {
        $option = get_option(self::NOTICE_FIELD);
        $message = isset($option['message']) ? $option['message'] : false;
        $severity = !empty($option['severity']) ? $option['severity'] : 'error';

        if ($message) {
            echo "<div class='notice notice-{$severity} is-dismissible'><p>{$message}</p></div>";
            delete_option(self::NOTICE_FIELD);
        }
    }

    public static function error($message) {
        self::update_option($message, 'error');
    }

    public static function warning($message) {
        self::update_option($message, 'warning');
    }

    public static function info($message) {
        self::update_option($message, 'info');
    }

    public static function success($message) {
        self::update_option($message, 'success');
    }

    private static function update_option($message, $severity) {
        update_option(self::NOTICE_FIELD, [
            'message' => $message,
            'severity' => $severity
        ]);
    }

}
