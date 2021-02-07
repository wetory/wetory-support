<?php
/**
 * Template to render posts table.
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
            <th><?php _e('Published', 'wetory-support'); ?></th>
            <th><?php _e('Title', 'wetory-support'); ?></th>
            <th><?php _e('Author', 'wetory-support'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php while (have_posts()) : the_post(); ?>
            <tr>
                <td>
                    <?php echo get_the_date(); ?>
                </td>
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
                <td>
                    <?php echo get_the_author(); ?>
                </td>
            </tr>
        <?php endwhile; ?> 
        <?php wp_reset_postdata(); ?>
    </tbody>
</table>

