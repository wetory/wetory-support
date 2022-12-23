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
    <ul class="wetory-support-sub-tab">
        <li data-target="general"><a><?php echo esc_html__('General', 'wetory-support'); ?></a></li>
        <li data-target="maintenance"><a><?php echo esc_html__('Maintenance', 'wetory-support'); ?></a></li>
        <li data-target="security"><a><?php echo esc_html__('Security', 'wetory-support'); ?></a></li>
    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="general" style="display:block;">
            <div class="wetory-support-settings-section general">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="maintenance">
            <div class="wetory-support-settings-section maintenance">
                <?php $section_name = 'maintenance'; ?>
                <h3><?php echo esc_html__('Maintenance', 'wetory-support'); ?></h3>
                <p><?php _e('Customize maintenance mode behaviour.', 'wetory-support'); ?></p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Maintenance mode page', 'wetory-support'); ?></th>
                        <td>
                            <input type="button" name="create" class="mp-operation button" value="<?php _e('Create maintenance page', 'wetory-support'); ?>" data-working-text="<?php _e('Creating page...', 'wetory-support'); ?>"> 
                            <input type="button" name="delete" class="mp-operation button" value="<?php _e('Delete maintenance page', 'wetory-support'); ?>" data-working-text="<?php _e('Deleting page...', 'wetory-support'); ?>">     
                            <span class="settings-field-description"><?php _e('Plugin provides template for maintenance page which is shown instead of default maintenance notification panel.', 'wetory-support'); ?></span>
                        </td>
                    </tr>
                </table>
                <?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON === true): ?>
                    <div class="alert alert-warning" role="alert">
                        <?php _e('<a href="https://developer.wordpress.org/plugins/cron/" targte="_blank">WP-Cron</a> is disabled on this website. Automatic custom maintenance page recreation will not work as it is using WP-Cron for event scheduling.', 'wetory-support'); ?>
                    </div>
                <?php endif; ?>
                <table class="form-table">
                <?php                    
                    $settings_field = array(
                        'label' => __('Disable recreate of custom maintenance page', 'wetory-support'),
                        'type' => 'checkbox',
                        'option_section' => $section_name,
                        'option_key' => 'maintenance-page',
                        'id' => 'disable-autorecreate',
                        'name' => 'disable-autorecreate',
                        'description' => __('Custom maintenance page is by default automatically recreated by WP-Cron scheduled events.', 'wetory-support'),
                    );
                    Wetory_Support_Settings_Renderer::render_settings_field($settings_field);
                ?>
                </table>
            </div>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="security">
            <div class="wetory-support-settings-section security">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </div>
</div>