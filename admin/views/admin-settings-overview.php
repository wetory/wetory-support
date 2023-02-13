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
            <table id="settings-overview-table" class="widefat groupped-values">
                <thead>
                    <tr>
                        <th><?php _e('Section', 'wetory-support'); ?></th>
                        <th><?php _e('Key', 'wetory-support'); ?></th>
                        <th><?php _e('Name', 'wetory-support'); ?></th>
                        <th colspan="2"><?php _e('Value', 'wetory-support'); ?></th>
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
                                        $setting_thickbox_id = $section . '-' . $key . '-' . $name;
                                        printf(
                                            '<tr class="%1$s">
                                                <td class="%2$s">%3$s</td>
                                                <td class="%4$s">%5$s</td>
                                                <td>%6$s</td>
                                                <td>%7$s</td>
                                                <td style="width: 10px"><a href="#TB_inline?&width=500&height=250&inlineId=settings-in-code" class="thickbox" title="%8$s" data-section="%3$s" data-key="%5$s" data-name="%6$s"><span class="dashicons dashicons-editor-code"></span></a></td>
                                            </tr>',
                                            ($section_out || $key_out) ? 'group' : '',
                                            $section_out ? '' : 'group',
                                            $section,
                                            $key_out ? '' : 'group',
                                            $key,
                                            $name,
                                            $value,
                                            __('How to use this configurations setting value', 'wetory-support')
                                        );
                                        $section_out = false;
                                        $key_out = false;
                                    endforeach;
                                } else {
                                    $name = $key;
                                    $value = $data;
                                    $setting_thickbox_id = $section . '-' . $name;
                                    printf(
                                        '<tr class="%1$s">
                                            <td class="%2$s">%3$s</td>
                                            <td class="%4$s">%5$s</td>
                                            <td>%6$s</td>
                                            <td>%7$s</td>
                                            <td style="width: 10px"><a href="#TB_inline?&width=500&height=250&inlineId=settings-in-code" class="thickbox" title="%8$s" data-section="%3$s" data-key="%5$s" data-name="%6$s"><span class="dashicons dashicons-editor-code"></span></a></td>
                                        </tr>',
                                        ($section_out || $key_out) ? 'group' : '',
                                        $section_out ? '' : 'group',
                                        $section,
                                        $key_out ? '' : 'group',
                                        '-',
                                        $name,
                                        $value,
                                        __('How to use this configurations setting value', 'wetory-support')
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
            <div id="settings-in-code" style="display:none;">
                <p><?php _e('Query configuration settings value in PHP:', 'wetory-support'); ?></p>
                <pre><code>$value = Wetory_Support_Options::get_settings_value(
    array(
        'option_name' => '<?php echo WETORY_SUPPORT_SETTINGS_OPTION; ?>' // This is optional
        'option_section' => <span id="settings-in-code-section">maintenance</span>,
        'option_key' => <span id="settings-in-code-key">maintenance-page</span>,
        'name' => <span id="settings-in-code-name">disable-autorecreate</span>,
    )
);</code></pre>
            </div>
        </div>
    </div>
</div>