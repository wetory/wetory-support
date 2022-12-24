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
        <div class="wetory-support-sub-tab-content" data-id="help-links" style="display:block;">
            <h3><?php echo esc_html__('Help links', 'wetory-support'); ?></h3>
            <p><?php _e('Use below links to get started or get help and support.', 'wetory-support'); ?></p>
            <ul class="wetory-support-help-links">
                <li>
                    <img src="<?php echo esc_url($wetory_support_images_path); ?>documentation.png">
                    <h3><?php echo esc_html__('Documentation', 'wetory-support'); ?></h3>
                    <p><?php echo esc_html__('Refer to our documentation to set and get started', 'wetory-support'); ?></p>
                    <a target="_blank" href="<?php echo WETORY_SUPPORT_URL_DOCUMENTATION ?>" class="button button-primary">
                        <?php echo esc_html__('Documentation', 'wetory-support'); ?>
                    </a>
                </li>
                <li>
                    <img src="<?php echo esc_url($wetory_support_images_path); ?>support.png">
                    <h3><?php echo esc_html__('Help and Support', 'wetory-support'); ?></h3>
                    <p><?php echo esc_html__('We would love to help you on any queries or issues.', 'wetory-support'); ?></p>
                    <a target="_blank" href="<?php echo WETORY_SUPPORT_URL_CONTACT ?>" class="button button-primary">
                        <?php echo esc_html__('Contact', 'wetory-support'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>