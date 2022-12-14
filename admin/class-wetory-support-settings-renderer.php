<?php

/**
 * Settings fields renderer callbacks class.
 *
 * Static class can be used for callbacks for repeatable field callback in function add_settings_field
 * https://developer.wordpress.org/reference/functions/add_settings_field/
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

use Wetory_Support_Options as Options;

class Wetory_Support_Settings_Renderer
{

    /**
     * Render tabs navigation for settings page
     * @since 1.1.1
     * @param array $tabs Associative array of keys and tab labels
     * @return void
     */
    public static function render_settings_tabs($tabs)
    {
        foreach ($tabs as $key => $label) {
?>
            <a class="nav-tab" href="#<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></a>
<?php
        }
    }

    /**
     * Powerful helper function for rendering settings sections. 
     * 
     * Use it for rendering settings sections with settings fields on admin pages. 
     * 
     * Example of input
     * 
     * $args = array(
     *      'name'          => 'my-section',
     *      'title'         => 'My Section',
     *      'description'   => 'Description',
     *      'settings_fields' => array(
     *          ...
     *          settings fields definition goes here @see render_settings_field function  
     *          ...
     *      ),
     *      'debug'         => true|false|null,
     *      'before'        => HTML or string to be print before input,
     *      'after'         => HTML or string to be print after input,
     * }
     * 
     * @since      1.2.1
     * @param array $args Arguments
     */
    public static function render_settings_section($args)
    {

        // Apply defaults
        $defaults = array(
            'title_element' => 'h3',
        );
        $args = wp_parse_args($args, $defaults);

        if (!isset($args['name'])) {
            $args['name'] = isset($args['title']) ? sanitize_title($args['title']) : '';
        }

        // We can enable debigguing to view what comes into function
        if (isset($args['debug']) && $args['debug']) {
            echo '<pre>';
            echo 'Arguments: ';
            var_dump($args);
            echo '</pre>';
        }

        if (isset($args['before'])) {
            echo $args['before'];
        }

        // Wrap setting into form table and section container - START
        echo '<div class="wetory-support-settings-section ' . $args['name'] . '">';

        if (isset($args['title'])) {
            echo '<'.$args['title_element'].'>' . esc_html($args['title']) . '</'.$args['title_element'].'>';
        }

        if (isset($args['description'])) {
            echo $args['description'];
        }

        // Form table - START
        echo '<table class="form-table">';

        // Render settings fields if any
        if (isset($args['settings_fields']) && sizeof($args['settings_fields']) > 0) {
            $settings_fields = $args['settings_fields'];
            foreach ($settings_fields as $settings_field) {
                self::render_settings_field($settings_field);
            }
        }

        // Form table - START
        echo '</table>';

        // Wrap setting into form table and section container - END
        echo '</div>';

        if (isset($args['after'])) {
            echo $args['after'];
        }
    }

    /**
     * Powerful helper function for rendering settings field. 
     * 
     * Use it for rendering settings field on admin pages. Initially it was copied 
     * from below listed website post, but modified during the time.
     * 
     * Example of input
     * 
     * $args = array(
     *      'label'          => 'My Option Label',
     *      'type'           => 'input',
     *      'option_name'    => 'my_option_name',
     *      'option_section' => 'my_option_section',
     *      'option_key'     => 'my_option_key',
     *      'id'             => 'my_field_id',
     *      'name'           => 'my_field_name',
     *      'help'           => 'help_text'
     *      'description'    => 'description_text'
     *      'link'           => 'lint_to_documentation'
     *      'required'       => 'true|false|null',
     *      'required'       => 'true|false|null',
     *      'options'        => array (
     *           'value1'  => 'Label 1',
     *           'value2'  => 'Label 2',
     *           'value3'  => 'Label 3',
     *       ),
     *      'debug'          => true|false|null,
     *      'before'         => HTML or string to be print before input,
     *      'after'          => HTML or string to be print after input,
     * }
     * 
     * https://blog.wplauncher.com/create-wordpress-plugin-settings-page/
     * https://developer.wordpress.org/reference/functions/add_settings_field/
     * 
     * @since      1.0.0
     * @param array $args Arguments that are passed to callback function add_settings_field
     */
    public static function render_settings_field($args)
    {

        // Apply defaults
        $defaults = array(
            'option_name' => WETORY_SUPPORT_OPTION,
            'option_section' => 'general',
        );
        $args = wp_parse_args($args, $defaults);

        if (isset($args['before'])) {
            echo $args['before'];
        }

        // Construct option name        
        $name  = isset($args['option_name']) ? $args['option_name'] : '';
        $name .= isset($args['option_section']) ? '[' . $args['option_section'] . ']' : '';
        $name .= isset($args['option_key']) ? '[' . $args['option_key'] . ']' : '';
        $name .= '[' . $args['name'] . ']';

        // Get value
        $option_value = Options::get_value($args, null);

        // We can enable debigguing to view what comes into function
        if (isset($args['debug']) && $args['debug']) {
            echo '<pre>';
            echo 'Arguments: ';
            var_dump($args);
            echo '<br>Value: ';
            var_dump($option_value);
            echo '</pre>';
        }

        // Field is required/disabled?
        $required = (isset($args['required']) && $args['required']) ? 'required' : '';
        $disabled = (isset($args['disabled']) && $args['disabled']) ? 'disabled' : '';

        // One field = row in form table
        echo '<tr>';

        // Render label
        if (isset($args['label'])) {
            printf(
                '<th scope="row"><label for="%1$s">%2$s</label></th>',
                $name,
                $args['label']
            );
        }        

        // Render different input types
        echo '<td>';
        switch ($args['type']) {

            case 'checkbox':
                printf(
                    '<input type="checkbox" id="%1$s" name="%2$s" %3$s %4$s/>',
                    $args['id'],
                    $name,
                    $required,
                    checked('on', $option_value, false)
                );
                break;

            case 'select':
                printf('<select name="%1$s" id="%2$s">', $name, $args['id']);
                if (isset($args['options'])) {
                    foreach ($args['options'] as $value => $label) {
                        $selected = (isset($option_value) && $option_value == $value) ? 'selected' : '';
                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                    }
                } else {
                    wetory_write_log(__("No options list found in arguments! This parameter is required when using select type in render_settings_field callback.", 'wetory-support'));
                }
                echo '</select>';
                break;

            default:
                $step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
                $min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
                $max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';

                printf(
                    '<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" %5$s %6$s %7$s %8$s %9$s/>',
                    $args['type'],
                    $args['id'],
                    $name,
                    esc_attr($option_value),
                    $required,
                    $step,
                    $min,
                    $max,
                    $disabled
                );
                break;
        }
        if (isset($args['help'])) {
            self::render_tooltip($args['help']);
        }
        if (isset($args['link'])) {
            self::render_link_button($args['link'], __('More info', 'wetory-support'));
        } 
        echo '</td>';
           
        if (isset($args['description'])) {
            echo '<td><div class="description"><small>' . $args['description'] . '</small></div></td>';
        }   

        echo '</tr>';

        if (isset($args['after'])) {
            echo $args['after'];
        }
    }

    /**
     * Powerful helper function for rendering custom setting form.  
     * 
     * Use it for rendering settings forms on admin pages. If you are not using Settings API
     * then you can display settings options in nice table way. This function is producing
     * HTML table where option keys are represented as rows and option values are columns.
     * 
     * Columns specification has to match data structure as values are retrieved based on 
     * column key. 
     * 
     * Example of input:
     * $args = array(
     *     'option_name' => 'plugin_option_name',
     *     'columns' => array(
     *         'title' => array(
     *              'label' => __('Library', 'wetory-support'),
     *              'type' => 'raw|checkbox|select|link',     
     *              'source' => 'souurce_name' - Where to get data by key from,
     *              'class' => 'css_class_name' - Class name to be applied to the column
     *          ),
     *     ),
     * }
     * 
     * https://codex.wordpress.org/Settings_API
     * 
     * @since      1.0.0
     * @param array $args Arguments that contains data specification. Used for forming data columns and their values.
     * @param array $data Data to be displayed in form table. Row is option key and column is option value.
     */
    public static function render_horizontal_form_table($args, $data)
    {

        if (!isset($args['columns']) || !isset($args['option_name'])) {
            return;
        }

        echo '<table class="form-table wetory-settings-table ' . $args['option_name'] . '" role="presentation">';

        echo '<thead><tr>';
        foreach ($args['columns'] as $column_id => $column_options) {
            $column_class = 'col-' . $column_options['type'];
            $column_class .= isset($column_options['class']) ? ' ' . $column_options['class'] : '';
            if (isset($column_options['help'])) {
                $column_text = self::render_tooltip($column_options['help'], false, $column_options['label']);
            } else {
                $column_text = $column_options['label'];
            }
            echo '<th class="' . $column_class . '">' . $column_text . '</td>';
        }
        echo '</tr></thead>';

        echo '<tbody>';

        foreach ($data as $item) {

            echo '<tr>';

            foreach ($args['columns'] as $column_id => $column_options) {

                $column_data = (isset($column_options['source'])) ? $item[$column_options['source']] : $item;
                $column_class = 'col-' . $column_options['type'];
                $column_class .= isset($column_options['class']) ? ' ' . $column_options['class'] : '';

                switch ($column_options['type']) {
                    case 'raw':
                        echo '<td class="' . $column_class . '">' . $column_data[$column_id] . '</td>';
                        break;

                    case 'link':
                        echo '<td class="' . $column_class . '">' . self::render_link_button($column_data[$column_id], __('More info', 'wetory-support'), false) . '</td>';
                        break;

                    case 'tooltip':
                        echo '<td class="' . $column_class . '">' . self::render_tooltip($column_data[$column_id], false) . '</td>';
                        break;

                    default:
                        unset($field_args);
                        $field_args = array(
                            'type' => $column_options['type'],
                            'option_name' => $args['option_name'],
                            'option_key' => $column_data['id'],
                            'id' => $column_data['id'] . '-' . $column_id,
                            'name' => $column_id,
                            'before' => '<td class="' . $column_class . '">',
                            'after' => '</td>',
                        );
                        if (isset($column_options['options'])) {
                            $field_args['options'] = $column_data[$column_options['options']];
                        }
                        self::render_settings_field($field_args);
                        break;
                }
            }
            echo '</tr>';
        }

        echo '  </tbody>';
        echo '</table>';
    }

    /**
     * Render information tool tip 
     * 
     * @since 1.0.0
     * @param string $tooltip_text Information to be shown
     * @param bool $echo Immediately render or just return HTML markup, by default true
     * @param string $content Content elements to be wrapped by tool tip
     * @return string Tool tip HTML markup
     */
    public static function render_tooltip($tooltip_text, $echo = true, $content = ' ? ')
    {
        $html = '<div class="tooltip">' . $content . '<span class="tooltip-text">' . $tooltip_text . '</span></div>';
        if ($echo) {
            echo $html;
        } else {
            return $html;
        }
    }

    /**
     * Render button with link
     * 
     * @since 1.0.0
     * @param string $link Link location used in href attribute
     * @param string $content Content to be wrapped by button
     * @param bool $echo Immediately render or just return HTML markup, by default true
     * @param string $class Optional CSS class to be applied to button element
     * @return string Button HTML markup
     */
    public static function render_link_button($link, $content, $echo = true, $class = 'button button-primary')
    {
        $html = '<div class="link-button"><a class="' . $class . '" href="' . $link . '" target="_blank">' . $content . '</a></div>';
        if ($echo) {
            echo $html;
        } else {
            return $html;
        }
    }
}
