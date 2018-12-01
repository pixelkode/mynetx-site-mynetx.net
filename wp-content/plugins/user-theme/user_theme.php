<?php
/**
 * Plugin Name: User Theme
 * Plugin URI:  http://bueltge.de/wordpress-theme-in-abhaengigkeit-der-benuzter-id-oder-remote-adresse/530/
 * Description: Load a Theme for a special user (ID, IP, User_Level)
 * Version:     0.4.1
 * Author:      Frank Bültge
 * Author URI:  http://bueltge.de/
 * License:     GPLv3
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'template', 'fb_user_theme' );
add_filter( 'stylesheet', 'fb_user_theme' ); // only WP smaller 3*
add_filter( 'option_template', 'fb_user_theme' );
add_filter( 'option_stylesheet', 'fb_user_theme' );

function fb_user_theme( $template = '' ) {
	global $user_ID;
	
	// en_US: replace space in the name of a theme-folder with _ (underline)!
	// de_DE: Leerzeichen im Namen des Themes muessen mit _ (underline) ersetzt werden!

	// when profil-ID
	if ( in_array( $user_ID, array( 1, 21 ) ) ) {
		$template = 'default';
	}

	// when IP
	elseif ( in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '127.0.0.2' ) ) ) {
		$template = 'classic';
	}
	
	// when User has capabilities (example: Administrator -> manage_options)
	// @link see http://codex.wordpress.org/Roles_and_Capabilities
	elseif ( current_user_can( 'manage_options' ) ) {
		$template = 'classic';
	} elseif ( current_user_can( 'edit_posts' ) ) {
		$template = 'classic';
	} elseif ( current_user_can( 'read' ) ) {
		$template = 'default';
	}
	
	// when category with ID
	// @link http://codex.wordpress.org/Template_Tags/in_category
	elseif ( in_category( array( 1, 'example-name', 'Example Name' ) ) ) {
		$template = 'default';
	} else {
		$template = 'default';
	}
	
	return $template;
}