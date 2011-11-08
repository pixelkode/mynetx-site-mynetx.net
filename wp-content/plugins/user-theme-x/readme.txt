=== User Theme ===
Contributors: Bueltge
Donate link: http://bueltge.de/wunschliste/
Tags: user, theme, themes, admin, test, IP, ID, admin-interface
Requires at least: 1.5
Tested up to: 3.1-alpha
Stable tag: 0.1

It's a simple Plugin to load a Theme for a special user based on IP, User-ID, category or User-Level.
This plugin allows you to safely test an development drive a theme on your live-blog.

== Description ==
It's a simple Plugin to load a Theme for a special user based on IP, User-ID, category or User-Level.
This plugin allows you to safely test an development drive a theme on your live-blog.

You can use a different Theme for different user. The pluginis very small and easy, no options in the database-table and no options in backend of WordPress.

Hint: if you use a Theme-Framework, then is the hook `template` the name of the framework and the hook `stylesheet` the name of the activate child-theme. You must copy the function and add this with a different name. Follow a small example.

	`function fb_user_theme_template($template = '') {`
	`	`
	`	if ( current_user_can('manage_options') ) {`
	`		$template = 'genesis'; // framework`
	`	} elseif ( current_user_can('read') ) {`
	`		$template = 'twentyten';`
	`	}`
	`	`
	`	return $template;`
	`}`
	`add_filter('template', 'fb_user_theme_template');`
	`	`
	`function fb_user_theme_stylesheet($stylesheet = '') {`
	`	`
	`	if ( current_user_can('manage_options') ) {`
	`		$stylesheet = 'enterprise'; // childtheme`
	`	} elseif ( current_user_can('read') ) {`
	`		$stylesheet = 'twentyten';`
	`	}`
	`	`
	`	return $stylesheet;`
	`}`
	`add_filter('stylesheet', 'fb_user_theme_stylesheet');`


= Interested in WordPress tips and tricks =
You may also be interested in WordPress tips and tricks at [WP Engineer](http://wpengineer.com/) or for german people [bueltge.de](http://bueltge.de/) 

== Installation ==
1. Unpack the download-package
1. Edit the php-file with your ID, User-Level or IP and Theme-Name etc.

	* `$template` Variable for theme-name (db-name of theme)
	* `if ( array( 1, 21 ) )` ID of user

1. Upload the files to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Ready

See on [the official website](http://bueltge.de/wordpress-theme-in-abhaengigkeit-der-benuzter-id-oder-remote-adresse/530/ "User Theme").

== Frequently Asked Questions ==

= Where can I get more information? =

Please visit [the official website](http://bueltge.de/wordpress-theme-in-abhaengigkeit-der-benuzter-id-oder-remote-adresse/530/ "User Theme") for the latest information on this plugin.

= I love this plugin! How can I show the developer how much I appreciate his work? =

Please visit [the official website](http://bueltge.de/wordpress-theme-in-abhaengigkeit-der-benuzter-id-oder-remote-adresse/530/ "User Theme") and let him know your care or see the [wishlist](http://bueltge.de/wunschliste/ "Wishlist") of the author.
