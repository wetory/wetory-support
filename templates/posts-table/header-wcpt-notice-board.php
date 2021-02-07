<?php
/**
 * Template to render notice board posts table header.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates/posts-table
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