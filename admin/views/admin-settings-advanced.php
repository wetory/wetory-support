<?php

/**
 * Provide a admin area view for the plugin settings
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

<div class="wetory-support-tab-content" data-id="<?php echo esc_attr($target_id); ?>">
    <div class="wetory-support-sub-tab-container">
            <?php
            // @see Wetory_Support_Widgets_Controller::settings_section()
            do_action('wetory_settings_render_section', 'debugging');
            ?>
    </div>
    <div class="wetory-support-settings-section plugin-settings">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Reset settings', 'wetory-support'); ?></th>
                        <td>
                            <input type="submit" name="wetory_support_ajax_reset_settings" value="<?php echo esc_html__('Delete settings and reset', 'wetory-support'); ?>" class="button-secondary danger" onclick="wetory_support_settings_btn_click(this.name); if(confirm('<?php echo esc_html__( 'Are you sure you want to delete all your settings?', 'wetory-support' ); ?>')){  }else{ return false;};" />
                            <span class="settings-field-description"><?php _e('Warning: Resets all your current settings to default.', 'wetory-support'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
</div>