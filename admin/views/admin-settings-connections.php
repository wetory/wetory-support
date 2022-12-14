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
    
    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="apikeys" style="display:block;">
            <?php do_action('wetory_support_settings_render_section','apikeys'); ?>
        </div>
    </div>
</div>