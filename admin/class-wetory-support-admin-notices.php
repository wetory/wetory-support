<?php

/**
 * Class handling admin notices easily.
 *
 * @link       https://www.wetory.eu/
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Admin_Notice {

    const NOTICE_FIELD = 'wetory-support-admin_notice_message';

    public function display_notice() {
        $option = get_option(self::NOTICE_FIELD);
        $message = isset($option['message']) ? $option['message'] : false;
        $noticeLevel = !empty($option['notice-level']) ? $option['notice-level'] : 'notice-error';

        if ($message) {
            echo "<div class='notice {$noticeLevel} is-dismissible'><p>{$message}</p></div>";
            delete_option(self::NOTICE_FIELD);
        }
    }

    public static function displayError($message) {
        self::updateOption($message, 'notice-error');
    }

    public static function displayWarning($message) {
        self::updateOption($message, 'notice-warning');
    }

    public static function displayInfo($message) {
        self::updateOption($message, 'notice-info');
    }

    public static function displaySuccess($message) {
        self::updateOption($message, 'notice-success');
    }

    protected static function updateOption($message, $noticeLevel) {
        update_option(self::NOTICE_FIELD, [
            'message' => $message,
            'notice-level' => $noticeLevel
        ]);
    }

}
