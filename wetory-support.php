<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wetory.eu/
 * @since             1.0.0
 * @package           wetory_support
 *
 * @wordpress-plugin
 * Plugin Name:       Wetory Support
 * Plugin URI:        https://www.wetory.eu/
 * Description:       Contains some basics for website projects, that improve usability and development. Brings new reusable elements and third party libraries to WordPress. Prerequisite for other Wetory plugins. More info on plugin's website.
 * Version:           1.0.0
 * Author:            Tomáš Rybnický
 * Author URI:        https://www.wetory.eu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wetory-support
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Define some reusable path constants
 */
if (!defined('WETORY_SUPPORT_FILE')) {
    define('WETORY_SUPPORT_FILE', __FILE__);
}
if (!defined('WETORY_SUPPORT_BASENAME')) {
    define('WETORY_SUPPORT_BASENAME', plugin_basename(__FILE__));
}
if (!defined('WETORY_SUPPORT_PATH')) {
    define('WETORY_SUPPORT_PATH', plugin_dir_path(WETORY_SUPPORT_FILE));
}
if (!defined('WETORY_SUPPORT_URL')) {
    define('WETORY_SUPPORT_URL', plugin_dir_url(WETORY_SUPPORT_FILE));
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WETORY_SUPPORT_VERSION', '1.0.0');

/**
 * Label can be used in several places
 */
define('WETORY_LABEL', '#');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wetory-support-activator.php
 */
function activate_wetory_support() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wetory-support-activator.php';
    Wetory_Support_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wetory-support-deactivator.php
 */
function deactivate_wetory_support() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wetory-support-deactivator.php';
    Wetory_Support_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wetory_support');
register_deactivation_hook(__FILE__, 'deactivate_wetory_support');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wetory-support.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wetory_support() {

    $plugin = new Wetory_Support();
    $plugin->run();
}

run_wetory_support();
