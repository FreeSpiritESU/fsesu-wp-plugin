<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package         FreeSpiritESU
 * @subpackage      FreeSpiritESU/Includes
 * @copyright       Copyright (c) FreeSpirit ESU
 * @since           0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    01 February 2016
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    FreeSpiritESU
 * @subpackage FreeSpiritESU/Includes
 * @author     Richard Perry <richard@freespiritesu.org.uk>
 */

namespace FreeSpiritESU;

class i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fsesu',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
