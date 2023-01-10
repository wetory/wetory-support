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
        <li data-target="widgets"><a><?php echo esc_html__('Widgets', 'wetory-support'); ?></a></li>
        <li data-target="shortcodes"><a><?php echo esc_html__('Shortcodes', 'wetory-support'); ?></a></li>
        <li data-target="cpt"><a><?php echo esc_html__('Custom post types', 'wetory-support'); ?></a></li>

    </ul>
    <div class="wetory-support-sub-tab-container">
        <div class="wetory-support-sub-tab-content" data-id="widgets" style="display:block;">
            <?php
            // @see Wetory_Support_Widgets_Controller::settings_section()
            do_action('wetory_settings_render_section', 'widgets');
            ?>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="shortcodes" style="display:block;">
            <?php
            // @see Wetory_Support_Shortcodes_Controller::settings_section()
            do_action('wetory_settings_render_section', 'shortcodes');
            ?>
        </div>
        <div class="wetory-support-sub-tab-content" data-id="cpt">
            <div class="wetory-support-settings-section cpt">
                <h3 class="title"><?php _e('Custom post types', 'wetory-support'); ?></h3>
                <p class="description"><?php _e('Configure custom post types you want to use in your website. These are prepared most common post types ready to use with all meta properties.', 'wetory-support'); ?></p>
                <div class="alert alert-info" role="alert">
                    <?php _e('You need to refresh page to see effect of changed settings.', 'wetory-support'); ?>
                    <a onClick="window.location.reload()" class="alert-link reload-page"><?php _e('Reload page', 'wetory-support'); ?></a>
                </div>
                <?php
                // Convert objects to arrays
                $cpt_array_objects = array();
                foreach ($cpt_objects as $cpt_object) {
                    array_push($cpt_array_objects, $cpt_object->to_array());
                }

                // Prepare columns for form-table
                if ($cpt_objects) {
                    unset($args);
                    $args = array(
                        'option_section' => 'cpt',
                        'columns' => array(
                            'name' => array(
                                'label' => __('Post type', 'wetory-support'),
                                'type' => 'raw',
                            ),
                            'id' => array(
                                'label' => __('Post type key', 'wetory-support'),
                                'type' => 'raw',
                            ),
                            'use' => array(
                                'label' => __('Use', 'wetory-support'),
                                'type' => 'checkbox',
                                'help' => __('Check if you want to start using post type.', 'wetory-support'),
                            ),
                            'rewrite-slug' => array(
                                'label' => __('Rewrite slug', 'wetory-support'),
                                'type' => 'text',
                                'help' => __('Customize the permastruct slug. Defaults to post type key.', 'wetory-support'),
                            ),
                            'comments' => array(
                                'label' => __('Comments', 'wetory-support'),
                                'type' => 'checkbox',
                                'help' => __('Check if you want to allow comments for post type.', 'wetory-support'),
                            ),
                            'excerpt' => array(
                                'label' => __('Excerpt', 'wetory-support'),
                                'type' => 'checkbox',
                                'help' => __('Check if you want to allow excerpt for post type.', 'wetory-support'),
                            ),
                            'revisions' => array(
                                'label' => __('Revisions', 'wetory-support'),
                                'type' => 'checkbox',
                                'help' => __('Check if you want to allow revisions for post type.', 'wetory-support'),
                            ),
                            'published-posts' => array(
                                'label' => __('Published posts', 'wetory-support'),
                                'type' => 'raw',
                            ),
                            'description' => array(
                                'label' => '',
                                'type' => 'tooltip',
                                'class' => 'compact',
                            ),
                            'link' => array(
                                'label' => '',
                                'type' => 'link',
                                'source' => 'meta',
                                'class' => 'compact',
                            )
                        ),
                    );
                    // Render table using helper function
                    Wetory_Support_Settings_Renderer::render_horizontal_form_table($args, $cpt_array_objects);
                }
                ?>
            </div>
        </div>
    </div>
    <?php
	require 'admin-settings-button-save.php';
	?>
</div>