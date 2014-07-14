<?php
/**
 * @package Movies
 */
/*
Plugin Name: Movies
Plugin URI: https://github.com/zmcghee/wp-movies
Description: Offers WP integration with TMDb API. Also offers support for Events Calendar Pro.
Version: 0.1.0
Author: Zack McGhee
Author URI: https://github.com/zmcghee
License: BSD
Text Domain: movies
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