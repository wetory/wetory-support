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
        <li data-target="apikeys"><a><?php echo esc_html__('API Keys', 'wetory-support'); ?></a></li>
        <li data-target="links"><a><?php echo esc_html__('Links', 'wetory-support'); ?></a></li>
    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="apikeys" style="display:block;">
            <?php do_action('wetory_settings_render_section', 'apikeys'); ?>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="links" style="display:block;">
            <div class="wetory-support-settings-section links">
                <h3><?php echo esc_html__('Links', 'wetory-support'); ?></h3>
                <p><?php _e('Useful links to hidden plugins and external tools.', 'wetory-support'); ?></p>
                <table class="form-table">
                    <?php if (is_plugin_active('backwpup/backwpup.php') || true) : ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('BackWPup', 'wetory-support'); ?></th>
                            <td>
                                <a href="wp-admin/admin.php?page=backwpupjobs"><?php _e('Jobs', 'wetory-support'); ?></a> | 
                                <a href="wp-admin/admin.php?page=backwpuplogs"><?php _e('Logs', 'wetory-support'); ?></a> | 
                                <a href="https://wordpress.org/plugins/backwpup/" target="_blank" title="<?php _e('Go to plugin page', 'wetory-support'); ?>"><?php _e('Plugin page', 'wetory-support'); ?></a>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr valign="top">
                        <th scope="row"><?php _e('Wetory', 'wetory-support'); ?></th>
                        <td>
                            <a href="https://wm.z-wetory.eu/" target="_blank" title="<?php _e('Go to page', 'wetory-support'); ?>"><?php _e('Wordpress Manager', 'wetory-support'); ?></a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
    require 'admin-settings-button-save.php';
    ?>
</div>