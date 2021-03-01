<?php
/**
 * Template to render general posts filter.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */

// Get data passed to template if any
$post_type = isset($data->post_type) ? $data->post_type : 'post';

// Prepare some data for input fields
$args = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => 1,
    'taxonomy' => 'category',
    'pad_counts' => false
);

// $categories = get_categories($args);
$categories = wetory_get_categories_by_post_type($post_type, $args);
?>
<div class="panel panel-default wetory-ajax-filter-wrapper">
    <form class="wetory-ajax-filter" autocomplete="off" class="was-validated">
        <div class="panel-heading filter-heading">
            <span class="panel-title filter-title">
                <a data-toggle="collapse" href="#collapse"><i class="dashicons dashicons-filter"></i> <?php _e('Filter posts', 'wetory-support'); ?></a>
            </span>
        </div>
        <div id="collapse" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="filter-inputs">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="search" name="search" placeholder="<?php _e('Search by keywords', 'wetory-support'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">  
                        <div class="col-sm-12">
                            <label for="filter-category"><?php _e('Category', 'wetory-support'); ?></label>
                            <select class="form-control" id="category" name="category">
                                <option value="" disabled selected><?php _e('Select option', 'wetory-support'); ?></option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category->slug; ?>" ><?php echo $category->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label><?php _e('Published date', 'wetory-support'); ?></label>
                        </div>
                        <div class="form-group input-group col-sm-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php _e('From', 'wetory-support'); ?></span>
                            </div>
                            <input type="date" class="form-control" id="published_from" name="published_from">
                        </div>
                        <div class="form-group input-group col-sm-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php _e('To', 'wetory-support'); ?></span>
                            </div>
                            <input type="date" class="form-control" id="published_to" name="published_to">
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer form-group row filter-footer">
                <div class="col-sm-8">
                    <div class="filter-status">
                        <?php _e('Data loading...', 'wetory-support'); ?>
                    </div>
                    <div class="filter-summary" style="display: none;">
                        <?php _e('Found <span class="total-posts-count bold-text"></span> posts matching your criteria', 'wetory-support'); ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <button type="submit" class="float-right"><?php _e('Filter', 'wetory-support'); ?></button>
                    <button type="reset" class="float-right"><?php _e('Reset', 'wetory-support'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>