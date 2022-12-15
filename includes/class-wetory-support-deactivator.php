<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 * @package    wetory_support
 * @subpackage wetory_support/includes
 * @author     Tomáš Rybnický <tomas.rybnicky@wetory.eu>
 */
class Wetory_Support_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'wetory_run_routines_daily');
	}

}
