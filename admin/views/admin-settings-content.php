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

$shortcodes = $this->plugin_obj->get_plugin_shortcodes()->get_objects();
$widgets = $this->plugin_obj->get_plugin_widgets()->get_objects();
$cpt_objects = $this->plugin_obj->get_plugin_cpts()->get_objects();

?>

<div class="wetory-support-tab-content" data-id="<?php echo esc_attr($target_id); ?>">
    <ul class="wetory-support-sub-tab">
        <li data-target="general"><a><?php echo esc_html__('General', 'wetory-support'); ?></a></li>       
        <li data-target="cpt"><a><?php echo esc_html__('Custom post types', 'wetory-support'); ?></a></li>
    
    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="general" style="display:block;">            
            <?php do_action('wetory_support_settings_render_section','widgets'); ?>
            <?php do_action('wetory_support_settings_render_section','shortcodes'); ?>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="cpt">
            <div class="wetory-support-settings-section cpt">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </div>
</div>