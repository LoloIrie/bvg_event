<?php
if ( !defined( 'ABSPATH' ) ) die();
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://etalkers.org
 * @since      1.0.0
 *
 * @package    Bvg_Event
 * @subpackage Bvg_Event/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bvg_Event
 * @subpackage Bvg_Event/includes
 * @author     Laurent Dorier <lolo_irie@etalkers.org>
 */
class Bvg_Event_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bvg-event',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
