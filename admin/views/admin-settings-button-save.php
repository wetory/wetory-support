<?php

/**
 * Provide a admin area view for the plugin settings - Save button fragment
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.2.1
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin/views
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div style="clear: both;"></div>
<div class="wetory-support-tab-footer">
    <div class="wetory-support-row">
        <div class="wetory-support-col-6"></div>
        <div class="wetory-support-col-6">
            <input type="submit" name="wetory_support_ajax_update_settings" value="<?php echo esc_html__('Update Settings', 'wetory-support'); ?>" class="button-primary" style="float:right;" onClick="return wetory_support_settings_btn_click(this.name)" />
            <span class="spinner"></span>
        </div>
    </div>
</div>