<?php
/**
 * Template to render general posts grid item.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
// Get data passed to template if any
$idx = isset($data->idx) ? $data->idx : 0;
$columns = isset($data->columns) ? $data->columns : 2;

// Prepare some data
$col_class = 'col-md-' . (12 / $columns);
$img_src = wetory_get_post_thumbnail_url();

?> 
<div class="wetory-grid-item <?php echo $col_class; ?>">
    <div class="card">
        <img class="card-img-top" src="<?php echo $img_src; ?>" alt="Card image">
        <div class="card-body">
            <h4 class="card-title"><?php the_title(); ?></h4>
            <p class="card-text">
                <?php echo get_the_date(); ?>
            </p>
        </div>
    </div>
</div>