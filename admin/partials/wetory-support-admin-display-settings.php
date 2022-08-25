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
$wetory_images_path = WETORY_SUPPORT_URL . 'images/';
?>

<script type="text/javascript">
    var wetory_support_settings_success_message = '<?php echo esc_html__('Settings updated.', 'wetory-support'); ?>';
    var wetory_support_settings_error_message = '<?php echo esc_html__('Unable to update settings.', 'wetory-support'); ?>';
</script>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap wetory-support-settings">

    <h1 class="wp-heading-inline"><?php _e('Settings', 'wetory-support') ?></h1>  

    <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <?php settings_errors(); ?>      


    <div class="wetory-support-plugin-header">
        <p>
            <?php _e('Here you can modify plugin behavior. You can select what parts you want to use. Everything is disabled by default to prevent unnecesary loads.', 'wetory-support'); ?>
            <?php printf(__('Overview of all plugin settings can be found on <a href="%s">dashboard</a>.', 'wetory-support'), $this->links['dashboard']['url']); ?>
        </p>        
    </div>

    <div class="nav-tab-wrapper wetory-support-nav-tab-wrapper">
        <?php
        foreach ($tabs as $tab => $name) {
            $class = ( $tab == $active_tab ) ? 'nav-tab-active' : '';
            echo "<a class='nav-tab $class' href='?page=wetory-support-settings&tab=$tab'>$name</a>";
        }
        ?>
        <?php
        $tabs_arr = array(
            'wetory-support-settings-general' => __('General', 'wetory-support'),
            'wetory-support-settings-content' => __('Content', 'wetory-support'),
            'wetory-support-settings-cpt' => __('Custom post types', 'wetory-support'),
            'wetory-support-settings-connections' => __('Connections', 'wetory-support'),
            'wetory-support-settings-advanced' => __('Advanced', 'wetory-support'),
            'wetory-support-settings-overview' => __('Settings overview', 'wetory-support')
        );
        Wetory_Support_Settings_Renderer::render_settings_tabs($tabs_arr);
        ?>
    </div>

    <div id="wetory-support-tab-container" class="wetory-support-tab-container">
        <?php if ($active_tab == 'general'): ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('wetory-support-settings-general');
                do_settings_sections('wetory-support-settings-general');
                submit_button();
                ?>
            </form> 
        <?php elseif ($active_tab == 'cpt'): ?>
            <form method="POST">
                <input type="hidden" name="cpt_updated" value="true" />  
                <p><?php _e('Configure custom post types you want to use in your website.', 'wetory-support'); ?></p>              
                <?php
                wp_nonce_field('wetory_support_settings_cpt_update', 'wetory_support_settings_cpt_form');
                $this->render_cpt_form_table();
                submit_button();
                ?>
            </form>
        <?php elseif ($active_tab == 'apikeys'): ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('wetory-support-settings-apikeys');
                do_settings_sections('wetory-support-settings-apikeys');
                submit_button();
                ?>
            </form> 
        <?php endif; ?>
        
        <div style="clear: both;"></div>
        <div class="wetory-support-tab-footer">
            <div class="wetory-support-row">
                <div class="wetory-support-col-6"></div>
                <div class="wetory-support-col-6"><input type="submit" name="update_admin_settings_form" value="<?php echo esc_html__('Update Settings', 'wetory-support'); ?>" class="button-primary" style="float:right;" onClick="return cli_store_settings_btn_click(this.name)" />
                    <span class="spinner" style="margin-top:10px"></span>
                </div>
            </div>
        </div>
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
                <img src="<?php echo esc_url($wetory_images_path); ?>logo.png" alt="Wetory Logo">
            </div>
        </div>
    </div>
</div>