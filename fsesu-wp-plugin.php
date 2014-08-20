<?php
/* 
 Plugin Name:       FreeSpirit ESU Plugin
 Description:       Custom Plugin designed for use by FreeSpirit ESU only
 Version:           0.1.0
 Author:            Richard Perry
 Author URI:        http://richard.perry-online.me.uk/
 License:           GPL2+
 License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 Text Domain:       fsesu
 GitHub Theme URI:  richardp2/fsesu-theme
 GitHub Branch:     develop


    Copyright 2014  FreeSpirit ESU  (email : richard@freespiritesu.org.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'FSESU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Include the core class responsible for loading all necessary components of the plugin.
 */
require_once FSESU_PLUGIN_DIR . 'includes/class-fsesu-plugin.php';


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 */
register_activation_hook( __FILE__, array( 'FSESU_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'FSESU_Plugin', 'deactivate' ) );

/*
 * Initialise the plugin when plugins are loaded
 */
add_action( 'plugins_loaded', array( 'FSESU_Plugin', 'init' ) );
