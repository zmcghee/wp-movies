<?php

class Movies_Admin {
	public static function init() {
		add_action( 'admin_notices', array( 'Movies_Admin', 'display_notices' ) );
	}
	private static function menu() {
	
	}
	private static function display_notices() {
	    echo "<div id='notice' class='updated fade'><p>My Plugin is not configured yet. Please do it now.</p></div>\n";
	}
}

?>