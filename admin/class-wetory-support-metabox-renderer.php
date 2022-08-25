<?php

/**
 * Renderer class for rendering meta boxes in this plugin.
 *
 * Static methods can be used for rendering meta box with its fields. If some more
 * filed types needed then add them in this class by providing static rendering
 * method with appropriate output for given field.
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/admin
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Metabox_Renderer {

    const LABEL_CLASS = 'wetory-metabox-label';
    const INPUT_CLASS = 'wetory-metabox-input';
    const FIELD_CLASS = 'wetory-metabox-field';

    /**
     * Rendering fields 
     * @since 1.1.0
     * @param  array $fields Array of fields 
     * @return void  
     */
    public static function render_fields($fields) {
        // Check if some fields come
        if (!isset($fields)) {
            echo '<p>' . __('No fields in this metabox.', 'wetory-support') . '</p>';
            return;
        }
        // Iterate fileds and render based on field type by calling appropriate static method
        foreach ($fields as $name => $field) {
            self::{ 'render_' . $field['type'] }($field);
        }
    }

    /**
     * Render text field.
     * 
     * Supports: required
     * 
     * @since 1.1.0
     * @param  string $field options
     * @return void     
     */
    public static function render_text($field) {
        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>">
                    <?php echo $required ? $title . ' <span class="wetory-required">*</span>' : $title; ?>
                </label>
                <?php
                if ($desc != '') {
                    echo '<p class="description">' . $desc . '</p>';
                }
                ?>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $default; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required ? 'required' : ''; ?>/>	
            </div>
        </div>
        <?php
    }

    /**
     * Render textarea field
     * @since 1.1.0
     * @param  string $field options
     * @return void      
     */
    public static function render_textarea($field) {
        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>">
                    <?php echo $required ? $title . ' <span class="wetory-required">*</span>' : $title; ?>
                </label>
                <?php
                if ($desc != '') {
                    echo '<p class="description">' . $desc . '</p>';
                }
                ?>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required ? 'required' : ''; ?>><?php echo $default; ?></textarea>	
            </div>
        </div>

        <?php
    }

    /**
     * Render date field
     * @since 1.1.0
     * @param  string $field options
     * @return void     
     */
    public static function render_date($field) {
        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>">
                    <?php echo $required ? $title . ' <span class="wetory-required">*</span>' : $title; ?>
                </label>
                <?php
                if ($desc != '') {
                    echo '<p class="description">' . $desc . '</p>';
                }
                ?>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $default; ?>" placeholder="<?php echo $placeholder; ?>" class="wetory-datepicker" <?php echo $required ? 'required' : ''; ?>/>	
            </div>
        </div>
        <?php
    }

    /**
     * Render WPEditor field
     * @since 1.1.0
     * @param  string $field  options
     * @return void      
     */
    public static function render_wpeditor($field) {

        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>"><?php echo $title; ?></label>
                <?php
                if ($desc != '') {
                    echo '<p class="description">' . $desc . '</p>';
                }
                ?>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <?php wp_editor($default, $name, array('wpautop' => false)); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render select field
     * @since 1.1.0
     * @param  string $field options
     * @return void      
     */
    public static function render_select($field) {
        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <<div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>">
                    <?php echo $required ? $title . ' <span class="wetory-required">*</span>' : $title; ?>
                </label>
                <?php
                if ($desc != '') {
                    echo '<p class="description">' . $desc . '</p>';
                }
                ?>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" <?php echo $required ? 'required' : ''; ?>>
                    <?php
                    foreach ($options as $value => $text) {
                        echo '<option ' . selected($default, $value, false) . ' value="' . $value . '">' . $text . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
    }

    /**
     * Render radio
     * @since 1.1.0
     * @param  string $field options
     * @return void      
     */
    public static function render_radio($field) {
        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>"><?php echo $title; ?></label>
                <?php
                if ($desc != '') {
                    echo '<p class="description">' . $desc . '</p>';
                }
                ?>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <?php
                foreach ($options as $value => $text) {
                    echo '<input name="' . $name . '" id="' . $name . '" type="' . $type . '" ' . checked($default, $value, false) . ' value="' . $value . '">' . $text . '</option><br/>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render checkbox field
     * @since 1.1.0
     * @param  string $field options
     * @return void      
     */
    public static function render_checkbox($field) {
        extract($field);
        ?>
        <div class="<?php echo self::FIELD_CLASS; ?>">
            <div class="<?php echo self::LABEL_CLASS; ?>">
                <label for="<?php echo $name; ?>"><?php echo $title; ?></label>
            </div>                
            <div class="<?php echo self::INPUT_CLASS; ?>">
                <input <?php checked($default, '1', true); ?> type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1" placeholder="<?php echo $placeholder; ?>" />
                <?php echo $desc; ?>
            </div>
        </div>
        <?php
    }

}
