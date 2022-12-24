<?php

/**
 * Provide a admin area view for the plugin settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin/partials
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Set up some useful variables
$wetory_support_images_path = WETORY_SUPPORT_URL . 'images/';
$wetory_support_admin_view_path = plugin_dir_path(WETORY_SUPPORT_FILE) . 'admin/views/';
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap wetory-support-settings">

    <h1 class="wp-heading-inline"><?php _e('Settings', 'wetory-support') ?></h1>

    <div class="wetory-support-plugin-header">
        <p>
            <?php _e('Here you can modify plugin behavior. You can select what parts you want to use. Everything is disabled by default to prevent unnecesary loads.', 'wetory-support'); ?>
        </p>        
    </div>

    <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <?php settings_errors(); ?>

    <div class="wetory-support-plugin-notifications"></div>

    <div class="nav-tab-wrapper wetory-support-nav-tab-wrapper">
        <?php
        $tabs_arr = array(
            'general' => __('General', 'wetory-support'),
            'content' => __('Content', 'wetory-support'),
            'connections' => __('Connections', 'wetory-support'),
            'advanced' => __('Advanced', 'wetory-support'),
            'overview' => __('Settings overview', 'wetory-support'),
            'help' => __('Help', 'wetory-support'),
        );
        Wetory_Support_Settings_Renderer::render_settings_tabs($tabs_arr);
        ?>
    </div>

    <div id="wetory-support-tab-container" class="wetory-support-tab-container">
        <?php
        $setting_views_a = array(
            'general' => 'admin-settings-general.php',
            'content' => 'admin-settings-content.php',
            'connections' => 'admin-settings-connections.php',
            'advanced' => 'admin-settings-advanced.php',
            'overview' => 'admin-settings-overview.php',
            'help' => 'admin-settings-help.php',
        );
        $setting_views_b = array(
            'wetory-support-settings-overview' => 'admin-settings-overview.php',
        );
        ?>
        <?php $form_action = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : ''; ?>
        <form method="post" action="<?php echo esc_url($form_action); ?>" id="wetory_support_settings_form">
            <input type="hidden" name="wetory_support_submit_action" value="" id="wetory_support_submit_action" />
            <?php
            if (function_exists('wp_nonce_field')) {
                wp_nonce_field('wetory-support-update-' . WETORY_SUPPORT_SETTINGS_OPTION);
            }
            foreach ($setting_views_a as $target_id => $value) {
                $settings_view = $wetory_support_admin_view_path . $value;
                if (file_exists($settings_view)) {
                    include $settings_view;
                }
            }
            ?>
            <?php
            foreach ($setting_views_b as $target_id => $value) {
                $settings_view = $wetory_support_admin_view_path . $value;
                if (file_exists($settings_view)) {
                    include $settings_view;
                }
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
        </form>
    </div>

    <div class="wetory-support-plugin-footer">
        <div class="wetory-support-plugin-branding">
            <div class="wetory-support-plugin-branding-tagline">
                <?php
                echo sprintf(
                    wp_kses(
                        __('Wetory Support %s &copy; %s | by <a href="%s" target="_blank">wetory</a>', 'wetory-support'),
                        array(
                            'a' => array(
                                'href' => array(),
                                'target' => array(),
                            ),
                        )
                    ),
                    'v' . WETORY_SUPPORT_VERSION,
                    date("Y"),
                    'https://www.wetory.eu/'
                );
                ?>
            </div>
            <div class="wetory-support-plugin-branding-logo">
                <img src="<?php echo esc_url($wetory_support_images_path); ?>logo.png" alt="Wetory Logo">
            </div>
        </div>
    </div>
</div>