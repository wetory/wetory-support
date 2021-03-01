<?php
/**
 * Template to render no data message.
 *
 * @since      1.1.0
 * 
 * @package    wetory_support
 * @subpackage wetory_support/templates
 * @author     Tomas Rybnicky <tomas.rybnicky@wetory.eu>
 */
?>
<div class="wetory-template no-results not-found">
    <header class="entry-header">
        <h2 class="entry-title"><?php esc_html_e('Nothing to display', 'wetory-support'); ?></h2>
    </header>
    <div class="entry-content">
        <p><?php esc_html_e('It seems that no data exists for requested content.', 'wetory-support'); ?></p>
    </div>
</div>
