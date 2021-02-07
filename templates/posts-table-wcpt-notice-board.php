<?php
/**
 * Template to render posts table for custom post type "Notice Board"
 * 
 * This post type is created by this plugin and can be enabled from plugin
 * settings. 
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
?>
<table class="table table-hover">
    <thead class="thead-dark">
        <tr>
            <th><?php _e('Title', 'wetory-support'); ?></th>
            <th><?php _e('Valid from', 'wetory-support'); ?></th>
            <th><?php _e('Valid to', 'wetory-support'); ?></th>
            <th><?php _e('Author', 'wetory-support'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php while (have_posts()) : the_post(); ?>
            <tr>
                <td>
                    <?php
                    printf(
                            '<a href="%s" title="%s">%s</a>',
                            get_permalink(),
                            __('Go to detail', 'wetory-support'),
                            get_the_title(),
                    );
                    ?>
                </td>     
                <?php
                $valid_from_meta = get_post_meta(get_the_ID(), 'valid_from', true);
                $valid_from = date_create_from_format("Y-m-d", $valid_from_meta);

                $valid_to_meta = get_post_meta(get_the_ID(), 'valid_to', true);
                $valid_to = date_create_from_format("Y-m-d", $valid_to_meta);
                ?>
                <td>
                    <?php echo wetory_get_formatted_date($valid_from); ?>
                </td>
                <td>
                    <?php echo wetory_get_formatted_date($valid_to); ?>
                </td>
                <td>
                    <?php echo get_the_author(); ?>
                </td>
            </tr>
        <?php endwhile; ?> 
        <?php wp_reset_postdata(); ?>
    </tbody>
</table>

