<?php

/**
 * Name: Favourite Posts
 * Description: Display your favourite posts. Just manually select posts to be displayed on the widget.
 *
 * Link: https://www.wetory.eu/ideas/
 * 
 * @package    wetory_support
 * @subpackage wetory_support/includes/widgets
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

class Widget_Wetory_Support_Favourite_Posts extends Wetory_Support_Widget {

    /**
     * Constructor for the widget
     *
     * @since    1.1.0
     */
    function __construct() {
        parent::__construct(
                'wetory_favourite_posts', // Base ID
                wetory_get_prefixed_label(__('Favourite Posts', 'wetory-support'), ' '), // Name
                array(
                    'description' => __('Add you favourite posts widget to the sidebar', 'wetory-support'),
                    'classname' => 'posts-widget wetory-favourite-posts'
                ) // Args
        );
    }

    /**
     * Load required sources for this widget.
     */
    protected function load_sources() {
        add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'load_admin_styles'));
    }

    /**
     * Loading scripts
     * 
     * https://developer.wordpress.org/reference/functions/wp_enqueue_script/     * 
     * @since 1.0.0
     */
    public function load_admin_scripts($hook) {
        if ($hook != 'widgets.php' || !is_admin()) {
            return;
        }
        
        // Select2 load can be prevented from settings
        $prevent = Wetory_Support_Options::get_settings_value(
            array(  
                'option_section' => 'third_party_libs',
                'option_key' => 'select2-4.0.13',
                'name' => 'prevent-in-admin',
            )
        );
        if(!isset($prevent)){
            wp_enqueue_script('select2', WETORY_SUPPORT_URL . 'assets/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);
        }

        wp_enqueue_script('wetory-favourite-posts-script', WETORY_SUPPORT_URL . 'admin/js/widgets/wetory-favourite-posts.min.js', array('jquery', 'select2'), WETORY_SUPPORT_VERSION, true);
    }
    
    /**
     * Loading styles
     * 
     * https://developer.wordpress.org/reference/functions/wp_enqueue_style/
     * @since 1.1.0
     */
    public function load_admin_styles($hook) {
        if ($hook != 'widgets.php') {
            return;
        }
        
        wp_enqueue_style('select2', WETORY_SUPPORT_URL . 'assets/select2/4.0.13/css/select2.min.css', array(), '4.0.13', 'all');
    }
    
    

    /**
     * Echoes the widget content. Overriding function WP_Widget::widget() 
     * 
     * @since 1.0.0
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
        $link_style = !empty($instance['link_style']) ? $instance['link_style'] : 'none';
        $link_title = !empty($instance['link_title']) ? $instance['link_title'] : null;
        $link_url = !empty($instance['link_url']) ? $instance['link_url'] : null;
        $link_align = !empty($instance['link_align']) ? $instance['link_align'] : 'left';
        $has_link = ($link_style !== 'none' && $link_title && $link_url);

        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Posts', 'wetory-support');

        // Put link to title 
        if ($has_link && $link_style === 'title') {
            $args['before_title'] = $args['before_title'] . '<a href=' . $link_url . '>';
            $args['after_title'] = '</a>' . $args['after_title'];
        }

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $selected_posts = $instance['selected_posts'];
        $list_style = !empty($instance['list_style']) ? $instance['list_style'] : 'ol';
        $show_date = !empty($instance['show_date']) ? (bool) $instance['show_date'] : false;
        $show_thumb = !empty($instance['show_thumb']) ? (bool) $instance['show_thumb'] : false;
        
        // query posts
        $r = new WP_Query(array(
            'post_type' => 'any',
            'post__in' => $selected_posts,
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
        ));

        if (!$r->have_posts()) {
            return;
        }

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $list_element = 'ul';
        $list_class = 'posts';
        $thumbnail_size = 'thumbnail';
        switch ($list_style) {
            case 'ul':
                $list_element = 'ul';
                break;
            case 'listing':
                $list_class = 'posts-listing';
                $thumbnail_size = 'wetory-listing-icon';
                break;
            case 'tiles':
                $list_class = 'posts-tiles';
                break;
            default:
                $list_element = 'ol';
                break;
        }
        ?>
        <?php echo '<' . $list_element . ' class="' . $list_class . '">'; ?>
        <?php foreach ($r->posts as $recent_post) : ?>
            <?php
            $post_title = get_the_title($recent_post->ID);
            $title = (!empty($post_title) ) ? $post_title : __('(no title)', 'wetory-support');
            ?>
            <li>
                <div class="post">
                    <?php if ($show_thumb) : ?>
                        <span class="post-thumbnail">
                            <a href="<?php the_permalink($recent_post->ID); ?>" title="<?php echo $title; ?>" aria-hidden="true">
                                <?php
                                echo has_post_thumbnail($recent_post->ID) ? get_the_post_thumbnail($recent_post->ID, $thumbnail_size) : '<div class="no-thumbnail"></div>';
                                ?>
                            </a>
                        </span>
                    <?php endif; ?>
                    <div class="post-content">
                        <h3 class="post-title">
                            <a href="<?php the_permalink($recent_post->ID); ?>"><?php echo $title; ?></a>
                        </h3>
                        <?php if ($show_date) : ?>
                            <p class="post-date"><?php echo get_the_date('', $recent_post->ID); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
        <?php echo '</' . $list_element . '>'; ?>
        <?php
        // Display link if widget has some
        if ($has_link) {
            printf('<div class="link-wrapper" style="text-align: %s;"><a class="%s" href=%s>%s</a></div>', $link_align, $link_style, $link_url, $link_title);
        }
        ?>
        <?php
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
        $instance['selected_posts'] = esc_sql($new_instance['selected_posts']);
        $instance['list_style'] = wp_strip_all_tags($new_instance['list_style']);
        $instance['show_date'] = !empty($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        $instance['show_thumb'] = !empty($new_instance['show_thumb']) ? (bool) $new_instance['show_thumb'] : false;
        $instance['link_style'] = wp_strip_all_tags($new_instance['link_style']);
        $instance['link_title'] = empty($new_instance['link_title']) ? '' : sanitize_text_field($new_instance['link_title']);
        $instance['link_url'] = empty($new_instance['link_url']) ? '' : esc_url($new_instance['link_url']);
        $instance['link_style'] = wp_strip_all_tags($new_instance['link_style']);
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
        $selected_posts = isset($instance['selected_posts']) ? $instance['selected_posts'] : array();
        $list_style = isset($instance['list_style']) ? $instance['list_style'] : 'ol';
        $show_date = !empty($instance['show_date']) ? (bool) $instance['show_date'] : false;
        $show_thumb = !empty($instance['show_thumb']) ? (bool) $instance['show_thumb'] : false;
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
                '<select class="widefat favourite-posts" id="%1$s" name="%3$s[]" multiple="multiple">',
                $this->get_field_id('selected_posts'),
                __('Select posts:', 'wetory-support'),
                $this->get_field_name('selected_posts')
        );

        $posts = new WP_Query(array(
            'post_type' => 'any',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        if ($posts->have_posts()) {
            while ($posts->have_posts()) : $posts->the_post();
                printf(
                        '<option value="%s" %s>%s</option>',
                        get_the_ID(),
                        in_array(get_the_ID(), $selected_posts) ? 'selected="selected"' : '',
                        get_the_title()
                );
            endwhile;
        }

        echo '</select></p>';
        ?>      

        <?php
        printf(
                '<p><label for="%1$s">%2$s</label>' .
                '<select class="widefat" id="%1$s" name="%3$s">',
                $this->get_field_id('list_style'),
                __('List style:', 'wetory-support'),
                $this->get_field_name('list_style')
        );



        $list_styles = array(
            'ol' => __('Ordered list', 'wetory-support'),
            'ul' => __('Unordered list', 'wetory-support'),
            'listing' => __('Listing', 'wetory-support'),
            'tiles' => __('Tiles', 'wetory-support'),
        );

        foreach ($list_styles as $value => $label) {
            printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    selected($value, $list_style, false),
                    __($label, 'wetory-support')
            );
        }
        echo '</select></p>';
        ?>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php esc_html_e('Display post date?', 'wetory-support'); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_thumb); ?> id="<?php echo $this->get_field_id('show_thumb'); ?>" name="<?php echo $this->get_field_name('show_thumb'); ?>" />
            <label for="<?php echo $this->get_field_id('show_thumb'); ?>"><?php esc_html_e('Display post thumbnail?', 'wetory-support'); ?></label>
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
            'link' => __('Text link', 'wetory-support'),
            'button' => __('Button', 'wetory-support'),
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
                <label for="<?php echo $this->get_field_id('link_title'); ?>"><?php esc_html_e('Link title:', 'wetory-support'); ?></label>
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
