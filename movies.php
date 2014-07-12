<?php
/**
 * @package Movies
 */
/*
Plugin Name: Movies
Plugin URI: https://github.com/zmcghee/wp-movies
Description: Offers WP integration with TMDb API. Also offers support for Events Calendar Pro.
Version: 0.0.1
Author: Zack McGhee
Author URI: https://github.com/zmcghee
License: GPLv2 or later
Text Domain: movies
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'MOVIES__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( MOVIES__PLUGIN_DIR . 'functions.php' );
require_once( MOVIES__PLUGIN_DIR . 'class.movies.php' );

register_activation_hook( __FILE__, array( 'Movies', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Movies', 'plugin_deactivation' ) );

add_action( 'init', array( 'Movies', 'init' ) );

if ( is_admin() ) {
	require_once( MOVIES__PLUGIN_DIR . 'class.movies-admin.php' );
	add_action( 'init', array( 'Movies_Admin', 'init' ) );
}

?>