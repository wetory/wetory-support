<?php
/**
 * Template to render notice board posts table row.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
?>
<tr>
    <?php
    printf(
            '<td style="white-space: normal;"><a href="%s" title="%s">%s</a></td>',
            get_permalink(),
            __('Go to detail', 'wetory-support'),
            get_the_title(),
    );

    $valid_from_meta = get_post_meta(get_the_ID(), 'valid_from', true); 
    $valid_from_text = $valid_from_meta && $valid_from_meta !== '' ? wetory_get_formatted_date($valid_from_meta) : ''; 

    $valid_to_meta = get_post_meta(get_the_ID(), 'valid_to', true);
    $valid_to_text = $valid_to_meta && $valid_to_meta !== '' ? wetory_get_formatted_date($valid_to_meta) : ''; 

    printf(
            '<td>%s</td>',
            $valid_from_text,
    );
    printf(
            '<td>%s</td>',
            $valid_to_text,
    );
    printf(
            '<td>%s</td>',
            get_the_author(),
    );
    ?>
</tr>