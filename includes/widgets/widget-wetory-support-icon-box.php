<?php

/**
 * Name: Icon Box
 * Description: Display icon box widget containing icon, text and link.
 * 
 * Link: https://www.wetory.eu/ideas/
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/widgets
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
class Widget_Wetory_Support_Icon_Box extends Wetory_Support_Widget {

    /**
     * Constructor for the widget
     *
     * @since    1.1.0
     */
    function __construct() {
        parent::__construct(
                'wetory_icon_box', // Base ID
                wetory_get_prefixed_label(__('Icon Box', 'wetory-support'), ' '), // Name
                array(
                    'description' => __('Add icon box widget', 'wetory-support'),
                    'classname' => 'posts-widget wetory-icon-box'
                ) // Args
        );
    }

    /**
     * Echoes the widget content. Overriding function WP_Widget::widget() 
     * 
     * @since 1.2.0
     * 
     * @see https://developer.wordpress.org/reference/classes/wp_widget/widget/
     * 
     * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current widget instance.
     */
    public function widget($args, $instance) {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        // Check link settings from widget        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $link_style = !empty($instance['link_style']) ? $instance['link_style'] : 'none';
        $link_title = !empty($instance['link_title']) ? $instance['link_title'] : __('Read more', 'wetory-support');
        $link_url = !empty($instance['link_url']) ? $instance['link_url'] : '';
        $link_align = !empty($instance['link_align']) ? $instance['link_align'] : 'left';
        $has_link = ($link_style !== 'none' && $link_url);

        // Put link to title 
        if ($link_url) {
            $args['before_title'] = $args['before_title'] . '<a href=' . $link_url . '>';
            $args['after_title'] = '</a>' . $args['after_title'];
        }

        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $icon_type = !empty($instance['icon_type']) ? $instance['icon_type'] : 'dashicons';
        $icon = !empty($instance['icon']) ? $instance['icon'] : null;
        $icon_text = !empty($instance['icon_text']) ? $instance['icon_text'] : null;

        echo $args['before_widget'];

        // Display icon
        if ($icon && $link_url) {
            echo '<div class="icon-wrapper">';
            if ($link_style == 'icon') {
                printf('<a href="%s"><span class="icon %s %s"></span></a>', $link_url, $icon_type, $icon);
            } else {
                printf('<span class="icon %s %s"></span>', $icon_type, $icon);
            }
            echo '</div>';
        }

        // Display title
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // Display text
        If ($icon_text) {
            printf('<div class="text-wrapper"><p>%s</p></div>', $icon_text);
        }

        // Display link if widget has some
        if (($link_style == 'button' || $link_style == 'link') && $link_url && $link_title) {
            printf('<div class="link-wrapper" style="text-align: %s;"><a class="%s" href=%s>%s</a></div>', $link_align, $link_style, $link_url, $link_title);
        }
        echo $args['after_widget'];
    }

    /**
     * Updates a particular instance of a widget. Overrding core function.
     * 
     * @since 1.0.0
     * 
     * @see https://developer.wordpress.org/reference/classes/wp_widget/update/
     * 
     * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Settings to save or bool false to cancel saving.
     */
    public function update($new_instance, $old_instance) {
        // processes widget options to be saved
        $instance = $old_instance;
        $instance['title'] = empty($new_instance['title']) ? '' : sanitize_text_field($new_instance['title']);
        $instance['icon_type'] = wp_strip_all_tags($new_instance['icon_type']);
        $instance['icon'] = empty($new_instance['icon']) ? '' : sanitize_text_field($new_instance['icon']);
        $instance['icon_text'] = empty($new_instance['icon_text']) ? '' : sanitize_textarea_field($new_instance['icon_text']);
        $instance['link_style'] = wp_strip_all_tags($new_instance['link_style']);
        $instance['link_title'] = empty($new_instance['link_title']) ? '' : sanitize_text_field($new_instance['link_title']);
        $instance['link_url'] = empty($new_instance['link_url']) ? '' : esc_url($new_instance['link_url']);
        $instance['link_align'] = wp_strip_all_tags($new_instance['link_align']);
        return $instance;
    }

    /**
     * Outputs the settings update form. Overrides core function.
     * 
     * @since 1.0.0
     * 
     * @see https://developer.wordpress.org/reference/classes/wp_widget/form/
     * 
     * @param array $instance Current settings.
     * @return string Default return is 'noform'.
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $icon_type = isset($instance['icon_type']) ? $instance['icon_type'] : 'dashicons';
        $icon = isset($instance['icon']) ? $instance['icon'] : '';
        $icon_text = isset($instance['icon_text']) ? $instance['icon_text'] : '';
        $link_style = isset($instance['link_style']) ? $instance['link_style'] : 'none';
        $link_title = isset($instance['link_title']) ? $instance['link_title'] : '';
        $link_url = isset($instance['link_url']) ? $instance['link_url'] : '';
        $link_align = isset($instance['link_align']) ? $instance['link_align'] : 'left';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'wetory-support'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <?php
        printf(
                '<p><label for="%1$s">%2$s</label>' .
                '<select class="widefat" id="%1$s" name="%3$s">',
                $this->get_field_id('icon_type'),
                __('Icon type:', 'wetory-support'),
                $this->get_field_name('icon_type')
        );

        $icon_types = array(
            'dashicons' => __('Dashicons', 'wetory-support'),
            'fa' => __('Favicons', 'wetory-support')
        );

        foreach ($icon_types as $value => $label) {
            printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    selected($value, $icon_type, false),
                    __($label, 'wetory-support')
            );
        }
        echo '</select></p>';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('icon'); ?>"><?php esc_html_e('Icon:', 'wetory-support'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('icon'); ?>" name="<?php echo $this->get_field_name('icon'); ?>" type="text" value="<?php echo esc_attr($icon); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('icon_text'); ?>"><?php esc_html_e('Text:', 'wetory-support'); ?></label>
            <textarea rows="3" class="widefat" id="<?php echo $this->get_field_id('icon_text'); ?>" name="<?php echo $this->get_field_name('icon_text'); ?>"><?php echo esc_attr($icon_text); ?></textarea>
        </p>

        <?php
        printf(
                '<p><label for="%1$s">%2$s</label>' .
                '<select class="widefat widget-link-style" id="%1$s" name="%3$s">',
                $this->get_field_id('link_style'),
                __('Link style:', 'wetory-support'),
                $this->get_field_name('link_style')
        );

        $link_styles = array(
            'none' => __('None', 'wetory-support'),
            'button' => __('Button', 'wetory-support'),
            'icon' => __('Icon', 'wetory-support'),
            'link' => __('Text link', 'wetory-support'),
            'title' => __('Widget title', 'wetory-support'),
        );

        foreach ($link_styles as $value => $label) {
            printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    selected($value, $link_style, false),
                    __($label, 'wetory-support')
            );
        }
        echo '</select></p>';
        ?>

        <div class="widget-link-options" style="<?php echo $link_style === 'none' ? 'display: none;' : ''; ?>">
            <p>
                <label for="<?php echo $this->get_field_id('link_title'); ?>"><?php esc_html_e('Link text:', 'wetory-support'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('link_title'); ?>" name="<?php echo $this->get_field_name('link_title'); ?>" type="text" value="<?php echo esc_attr($link_title); ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('link_url'); ?>"><?php esc_html_e('Link URL:', 'wetory-support'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="url" placeholder="https://" value="<?php echo esc_attr($link_url); ?>" />
            </p>

            <?php
            printf(
                    '<p><label for="%1$s">%2$s</label>' .
                    '<select class="widefat" id="%1$s" name="%3$s">',
                    $this->get_field_id('link_align'),
                    __('Link align:', 'wetory-support'),
                    $this->get_field_name('link_align')
            );



            $link_aligns = array(
                'left' => __('Left', 'wetory-support'),
                'center' => __('Center', 'wetory-support'),
                'right' => __('Right', 'wetory-support'),
            );

            foreach ($link_aligns as $value => $label) {
                printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr($value),
                        selected($value, $link_align, false),
                        __($label, 'wetory-support')
                );
            }
            echo '</select></p>';
            ?>

        </div><!--.widget-link-options-->

        <?php
    }

}
