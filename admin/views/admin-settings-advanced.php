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
        <li data-target="debugging"><a><?php echo esc_html__('Debugging', 'wetory-support'); ?></a></li>
    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="debugging" style="display:block;">
            <?php
            // @see Wetory_Support_Widgets_Controller::settings_section()
            do_action('wetory_settings_render_section', 'debugging');
            ?>
        </div>
    </div>
</div>