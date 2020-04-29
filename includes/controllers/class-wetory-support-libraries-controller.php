<?php

/**
 * Class managing external libraries.
 *
 * Loads libraries based on plugin settings.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    wetory_support
 * @subpackage wetory_support/includes/controllers
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
use Wetory_Support_Options as Plugin_Options;

class Wetory_Support_Libraries_Controller extends Wetory_Controller {

    const GLOB_FILTER = WETORY_SUPPORT_PATH . 'includes/libraries/*';
    
    /**
     * Member that holds information about area. It is settable only and used in
     * Wetory_Support_Libraries_Controller::register() function. So you can modify
     * enqueue for library based on option stored in database properly.
     * 
     * @var type 
     */
    private $page_area;

    /**
     * Create new instance
     * 
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Load all widgets from widgets folder. 
     * 
     * Naming convention based approach. Function just load array of widgets into
     * array then will register them in another function.
     * 
     * Example of naming convetion that need to be folowed:
     *  - file  widgets/widget-wetory-support-latest-posts.php
     *  - class Widget_Wetory_Support_Latest_Posts
     * 
     * @see Wetory_Support_Widgets_Controller::register_widgets()
     * 
     * @since    1.0.0
     */
    protected function load() {
        $this->objects = array();

        $library_folders = glob(self::GLOB_FILTER, GLOB_ONLYDIR);

        foreach ($library_folders as $library_folder) {

            $library_name = pathinfo($library_folder)['basename'];
            $library_meta = $this->read_meta_data($library_folder);

            $library_version_folders = glob($library_folder . '/*', GLOB_ONLYDIR);
            $library_versions = array();
            foreach ($library_version_folders as $library_version_folder) {
                $version_name = pathinfo($library_version_folder)['basename'];
                $library_versions[$version_name] = $version_name;
            }

            $library = array(
                'id' => strtolower($library_name),
                'title' => __(ucwords($library_name), 'wetory-support'),
                'folder' => $library_folder,
                'versions' => $library_versions,
            );

            if ($library_meta) {
                $library['meta'] = $library_meta;
            }

            array_push($this->objects, $library);
        }
    }

    /**
     * Function is entry point of outside call to enqueue all libraries to page area. 
     * It is important to set private member $page_area before call in appropruate page area.
     * If you want to include library add it to the libraries folder in plugin root
     * with it's versions in sub-folders and it will be automatically nqueued based on settings.
     * 
     * @see Wetory_Support_Libraries_Controller::load_libraries()
     * @see Wetory_Support_Libraries_Controller::enqueue_library($library, $area)
     * 
     * @since    1.0.0
     */
    public function register() {
        foreach ($this->objects as $library) {
            $this->enqueue_library($library['id'], $this->page_area);
        }
    }

    /**
     * Special function calling library function based on its name. It is important to
     * add private function with same name as library and let is accept $options array. 
     * Use Wetory_Support_Libraries::bootstrap($options) as example.
     * 
     * https://www.php.net/manual/en/functions.variable-functions.php
     * @param type $library
     * @param type $area
     */
    private function enqueue_library($library, $area) {
        if (Plugin_Options::use_library($library, $area)) {
            $options = Plugin_Options::get_library_options($library);
            if (method_exists($this, $library)) {
                $this->$library($options);
            }
        }
    }

    /**
     * Register the Bootstrap toolkit based on given options. It is loaded dynamically 
     * based on version number. Both from CDN and local server. Folders have to exists.
     * 
     * @since    1.0.0
     * @return mixed
     */
    private function bootstrap($options) {

        // This library is tied to version
        if (!isset($options['version'])) {
            wetory_write_log("Unable to load library. Bootstrap library require version to be loaded properly. Skipping operation...");
            return;
        }

        // Set paths to script and script files based on CDN option
        $css_path = isset($options['cdn']) && $options['cdn'] === 'on' ? 'https://stackpath.bootstrapcdn.com/bootstrap/' . $options['version'] . '/css/bootstrap.min.css' : WETORY_SUPPORT_URL . 'includes/libraries/bootstrap/' . $options['version'] . '/css/bootstrap.min.css';
        $js_path = isset($options['cdn']) && $options['cdn'] === 'on' ? 'https://stackpath.bootstrapcdn.com/bootstrap/' . $options['version'] . '/js/bootstrap.min.js' : WETORY_SUPPORT_URL . 'includes/libraries/bootstrap/' . $options['version'] . '/js/bootstrap.min.js';

        // Finally add enqueue style and script files
        wp_enqueue_style('bootstrap', $css_path, array(), $options['version'], 'all');
        wp_enqueue_script('bootstrap', $js_path, array('jquery'), $options['version'], true);
    }

    /**
     * Register the Select2 jQuery library based on given options. It is loaded dynamically 
     * based on version number. Both from CDN and local server. Folders have to exists.
     * 
     * @since    1.0.0
     * @return mixed
     */
    private function select2($options) {

        // This library is tied to version
        if (!isset($options['version'])) {
            wetory_write_log("Unable to load library. Select2 library require version to be loaded properly. Skipping operation...");
            return;
        }

        // Set paths to script and script files based on CDN option
        $css_path = isset($options['cdn']) && $options['cdn'] === 'on' ? 'https://cdn.jsdelivr.net/npm/select2@' . $options['version'] . '/dist/css/select2.min.css' : WETORY_SUPPORT_URL . 'includes/libraries/select2/' . $options['version'] . '/css/select2.min.css';
        $js_path = isset($options['cdn']) && $options['cdn'] === 'on' ? 'https://cdn.jsdelivr.net/npm/select2@' . $options['version'] . '/dist/js/select2.min.js' : WETORY_SUPPORT_URL . 'includes/libraries/select2/' . $options['version'] . '/js/select2.min.js';

        // Finally add enqueue style and script files
        wp_enqueue_style('select2', $css_path, array(), $options['version'], 'all');
        wp_enqueue_script('select2', $js_path, array('jquery'), $options['version'], true);
    }

    /**
     * Read meta information written in README file if it exists. Read first 8kb only
     * and meta data are read per line.
     * 
     * https://developer.wordpress.org/reference/functions/get_file_data/
     * 
     * @param string $folder Path to the library folder
     * @return array Meta data array containing description and link to documentation
     */
    protected function read_meta_data($folder) {
        $readme_file = $folder . '/README';
        if (!file_exists($readme_file)) {
            return false;
        }
        $meta = get_file_data($readme_file, array(
            'description' => 'Description',
            'link' => 'Link',
        ));

        return $meta;
    }

    /**
     * Make it possible to switch $page_area from outside of class.
     */
    public function set_admin_area() {
        $this->page_area = 'admin';
    }

    /**
     * Make it possible to modify $page_area from outside of class.
     */
    public function set_public_area() {
        $this->page_area = 'public';
    }

    protected function base_class(): string {
        return '';
    }

    protected function glob_filter(): string {
        return self::GLOB_FILTER;
    }
}
