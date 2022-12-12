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
        <li data-target="general"><a><?php echo esc_html__('Widgets', 'wetory-support'); ?></a></li>        
        <li data-target="general"><a><?php echo esc_html__('Shortcodes', 'wetory-support'); ?></a></li>
        <li data-target="cpt"><a><?php echo esc_html__('Custom post types', 'wetory-support'); ?></a></li>
    
    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="widgets" style="display:block;">
            <div class="wetory-support-settings-section widgets">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="shortcodes" style="display:block;">
            <div class="wetory-support-settings-section widgets">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="cpt">
            <div class="wetory-support-settings-section cpt">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </div>
</div>