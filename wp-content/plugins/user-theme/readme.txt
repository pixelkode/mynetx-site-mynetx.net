=== User Theme ===
Contributors: Bueltge
Tags: user, theme, themes, admin, test, IP, ID, admin-interface
Requires at least: 1.5
Tested up to: 3.6-alpha
Stable tag: trunk

It's a simple Plugin to load a Theme for a special user based on IP, User-ID, category or User-Level.

== Description ==
It's a simple Plugin to load a Theme for a special user based on IP, User-ID, category or User-Level.
This plugin allows you to safely test an development drive a theme on your live-blog.

You can use a different Theme for different user. The pluginis very small and easy, no options in the database-table and no options in backend of WordPress. You must always change the source.

**Hint:** if you use a Theme-Framework, then is the hook `template` the name of the framework and the hook `stylesheet` the name of the activate child-theme. You must copy the function and add this with a different name. Follow a small example.
	
	
	add_filter('template', 'fb_user_theme_template' );
	add_filter( 'option_template', 'fb_user_theme_template' );
	function fb_user_theme_template( $template = '' ) {
		
		if ( current_user_can('manage_options') ) {
			$template = 'genesis'; // framework
		} elseif ( current_user_can('read') ) {
			$template = 'twentyten';
		}
		
		return $template;
	}
	
	
	add_filter( 'stylesheet', 'fb_user_theme_stylesheet' );
	add_filter( 'option_stylesheet', 'fb_user_theme_stylesheet' );
	function fb_user_theme_stylesheet( $stylesheet = '' ) {
		
		if ( current_user_can('manage_options') ) {
			$stylesheet = 'enterprise'; // childtheme
		} elseif ( current_user_can('read') ) {
			$stylesheet = 'twentyten';
		}
		
		return $stylesheet;
	}
	


== Installation ==
1. Unpack the download-package
1. Edit the php-file with your ID, User-Level or IP and Theme-Name etc.

	* `$template` Variable for theme-name (db-name of theme)
	* `if ( array( 1, 21 ) )` ID of user

1. Upload the files to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Ready

== Changelog ==
= 0.4.1 (06/12/2013) =
* Remove BOM on file
