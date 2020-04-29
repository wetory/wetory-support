<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
    <h1 class="wp-heading-inline"><?php _e('Wetory Dashboard', 'wetory-support') ?></h1>  
    <a class="page-title-action" href="<?php echo $this->links['settings']['url']; ?>"><?php _e('Settings', 'wetory-support'); ?></a>

    <p>
        <?php _e('Brief overview of plugin usage. Maybe will be extended to the future.', 'wetory-support'); ?>        
    </p>
    <table class="widefat groupped-values">
        <thead>
            <tr>
                <th><?php _e('Option', 'wetory-support'); ?></th>
                <th><?php _e('Key', 'wetory-support'); ?></th>
                <th><?php _e('Subkey', 'wetory-support'); ?></th>
                <th><?php _e('Value', 'wetory-support'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($plugin_options as $option):
                $option_out = true;
                foreach (get_option($option) as $key => $value):
                    $key_out = true;
                    foreach ($value as $subkey => $subvalue):
                        ?>
                        <tr class="<?php echo ($key_out || $option_out) ? 'group' : ''; ?>">
                            <td class="<?php echo $option_out ? '' : 'group'; ?>"><?php echo $option; ?></td>
                            <td class="<?php echo $key_out ? '' : 'group'; ?>"><?php echo $key; ?></td>       
                            <td><?php echo $subkey; ?></td>                            
                            <td><?php echo $subvalue; ?></td>
                        </tr>
                        <?php
                        $option_out = false;
                        $key_out = false;
                    endforeach;
                endforeach;
            endforeach;
            ?>
        </tbody>
    </table>
    <p class="submit">
        <a class="button" href="<?php echo $this->links['settings']['url']; ?>"><?php _e('Settings', 'wetory-support'); ?></a>
    </p>
</div>