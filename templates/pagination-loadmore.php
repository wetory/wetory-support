<?php
/**
 * Template to render pagination.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
?>
<div class="wetory-template wetory-posts-pagination-loadmore">
    <?php
// wetory_var_dump($wp_query);
    $current_page = $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1;
    $posts_per_page = $wp_query->query_vars['posts_per_page'];
    $total_posts_count = $wp_query->found_posts;

    printf(__('Showing <span class="displayed-posts-count bold-text">%s</span> of <span class="total-posts-count bold-text">%s</span> posts', 'wetory-support'), $posts_per_page * $current_page, $total_posts_count);

    if (function_exists('wetory_load_more_button')) {
        wetory_load_more_button();
    }
    ?>   
</div>