<?php
if(is_file('wp-content/plugins/wordpressbackup/wpbdc.php'))
{ 
	define('WP_USE_THEMES', false);
	if ( !isset($wp_did_header) ) {
		$wp_did_header = true;
	}
	require_once('wp-load.php');
	require_once('wp-content/plugins/wordpressbackup/wpbdc.php');
}
?>