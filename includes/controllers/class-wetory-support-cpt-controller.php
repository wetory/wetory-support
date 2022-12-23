<?php

/**
 * Define the custom post types provided by plugin
 *
 * Loads custom post type objects from files in cpt folder. File need to meet
 * naming convention and contain custom post type class.
 *
 * @link       https://www.wetory.eu/
 * @since      1.1.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */

 use Wetory_Support_Sanitizer as Sanitizer;

class Wetory_Support_Cpt_Controller extends Wetory_Controller{
    
    const BASE_CLASS = WETORY_SUPPORT_PATH . 'includes/cpt/abstract-wetory-support-cpt.php';
    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/cpt/cpt-wetory-support-*.php';

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
        add_filter('wetory_settings_sanitize', array($this, 'sanitize_settings'), 10, 1);

    }
    
    public function get_instance($file): Wetory_Support_Cpt {
        parent::get_instance($file);
        return Wetory_Support_Cpt::create_instance($this->get_class($file));
    }

    protected function base_class(): string {
        return self::BASE_CLASS;
    }

    protected function glob_filter(): string {
        return self::GLOB_FILTER;
    }

    /**
     * Sanitize settings.
     * 
     * It is hooked into 'wetory_settings_sanitize' filter
     * which is used during read/write of settings.
     * 
     * @param array $settings Associative array representing plugin settings
     * 
     * @since    1.2.1
     */
    public function sanitize_settings($settings){        
        
        $cpt_objects = $this->get_objects();

        if ($cpt_objects) {
            foreach ($cpt_objects as $cpt_object) {
                $cpt_object_id = $cpt_object->get_id();

                // use
                if(isset($settings['cpt'][$cpt_object_id]['use']) && !empty($settings['cpt'][$cpt_object_id]['use'])){
                    $settings['cpt'][$cpt_object_id]['use'] = Sanitizer::sanitize_checkbox($settings['cpt'][$cpt_object_id]['use'], 'on');
                }

                // rewrite-slug
                if(isset($settings['cpt'][$cpt_object_id]['rewrite-slug']) && !empty($settings['cpt'][$cpt_object_id]['rewrite-slug'])){
                    $settings['cpt'][$cpt_object_id]['rewrite-slug'] = Sanitizer::sanitize_slug($settings['cpt'][$cpt_object_id]['rewrite-slug']);
                }

                // comments
                if(isset($settings['cpt'][$cpt_object_id]['comments']) && !empty($settings['cpt'][$cpt_object_id]['comments'])){
                    $settings['cpt'][$cpt_object_id]['comments'] = Sanitizer::sanitize_checkbox($settings['cpt'][$cpt_object_id]['comments'], 'on');
                }

                // excerpt
                if(isset($settings['cpt'][$cpt_object_id]['excerpt']) && !empty($settings['cpt'][$cpt_object_id]['excerpt'])){
                    $settings['cpt'][$cpt_object_id]['excerpt'] = Sanitizer::sanitize_checkbox($settings['cpt'][$cpt_object_id]['excerpt'], 'on');
                }

                // revisions
                if(isset($settings['cpt'][$cpt_object_id]['revisions']) && !empty($settings['cpt'][$cpt_object_id]['revisions'])){
                    $settings['cpt'][$cpt_object_id]['revisions'] = Sanitizer::sanitize_checkbox($settings['cpt'][$cpt_object_id]['revisions'], 'on');
                }
            }
        }   
        
        return $settings;
    }

}
