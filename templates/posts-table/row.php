<?php
/**
 * Template to render general posts table row.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
?>
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
