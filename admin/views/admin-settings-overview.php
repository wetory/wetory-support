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
        <div class="wetory-support-sub-tab-content" data-id="overview" style="display:block;">
            <h3><?php echo esc_html__('Plugin settings overview', 'wetory-support'); ?></h3>
            <p><?php _e('Brief overview of this plugin settings on one screen.', 'wetory-support'); ?></p>
            <div class="alert alert-info" role="alert">
                <?php _e('You must refresh the page to see the effect of the changed settings.', 'wetory-support'); ?>
                <a onClick="window.location.reload()" class="alert-link reload-page"><?php _e('Reload page', 'wetory-support'); ?></a>
            </div>
            <table class="widefat groupped-values">
                <thead>
                    <tr>
                        <th><?php _e('Section', 'wetory-support'); ?></th>
                        <th><?php _e('Key', 'wetory-support'); ?></th>
                        <th><?php _e('Name', 'wetory-support'); ?></th>
                        <th><?php _e('Value', 'wetory-support'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($plugin_settings as $section => $section_settings) :
                        $section_out = true;
                        if (isset($section_settings) && !empty($section_settings)) :
                            foreach ($section_settings as $key => $data) :
                                $key_out = true;
                                if (is_array($data)) {
                                    foreach ($data as $n => $v) :
                                        $name = $n;
                                        $value = $v;
                                        printf(
                                            '<tr class="%1$s">
                                                    <td class="%2$s">%3$s</td>
                                                    <td class="%4$s">%5$s</td>
                                                    <td>%6$s</td>
                                                    <td>%7$s</td>
                                                </tr>',
                                            ($section_out || $key_out) ? 'group' : '',
                                            $section_out ? '' : 'group',
                                            $section,
                                            $key_out ? '' : 'group',
                                            $key,
                                            $name,
                                            $value
                                        );
                                        $section_out = false;
                                        $key_out = false;
                                    endforeach;
                                } else {
                                    $name = $key;
                                    $value = $data;
                                    printf(
                                        '<tr class="%1$s">
                                                <td class="%2$s">%3$s</td>
                                                <td class="%4$s">%5$s</td>
                                                <td>%6$s</td>
                                                <td>%7$s</td>
                                            </tr>',
                                        ($section_out || $key_out) ? 'group' : '',
                                        $section_out ? '' : 'group',
                                        $section,
                                        $key_out ? '' : 'group',
                                        '-',
                                        $name,
                                        $value
                                    );
                                }
                                $section_out = false;
                                $key_out = false;
                            endforeach;
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>