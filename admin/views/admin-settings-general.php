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
                <h3><?php echo esc_html__('Maintenance', 'wetory-support'); ?></h3>
                <p><?php _e('Customize maintenance mode behaviour.', 'wetory-support'); ?></p>
                <h4><?php _e('Maintenance mode page', 'wetory-support'); ?></h4>
                <div class="mp-operations-wrapper">          
                    <p><?php _e('Plugin provides template for maintenance page which is shown instead of default maintenance notification panel.', 'wetory-support'); ?></p>
                    <input type="button" name="create" class="mp-operation button" value="<?php _e('Create maintenance page', 'wetory-support'); ?>" data-working-text="<?php _e('Creating page...', 'wetory-support'); ?>"> 
                    <input type="button" name="delete" class="mp-operation button" value="<?php _e('Delete maintenance page', 'wetory-support'); ?>" data-working-text="<?php _e('Deleting page...', 'wetory-support'); ?>">     
                    <span class="mp-operation-outcome"></span>
                </div>
            </div>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="security">
            <div class="wetory-support-settings-section security">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </div>
</div>