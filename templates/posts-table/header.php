<?php
/**
 * Template to render general posts table header.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */

$post_type = isset($data->post_type) ? $data->post_type : 'post';
$loadmore_template = isset($data->loadmore_template) ? $data->loadmore_template : 'row-post';

?>
<table class="wetory-table table table-hover wetory-ajax-post-list <?php echo $post_type; ?>" data-loadmore-template="<?php echo $loadmore_template;?>">
    <thead class="thead-dark">
        <tr>
            <th><?php _e('Published', 'wetory-support'); ?></th>
            <th><?php _e('Title', 'wetory-support'); ?></th>
            <th><?php _e('Author', 'wetory-support'); ?></th>
        </tr>
    </thead>
    <tbody>