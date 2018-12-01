<?php
/*
Plugin Name: WordTwit Pro
Plugin URI: http://bravenewcode.com/wordtwit-pro
Version: 3.0b2
Description: The professional version of the popular WordPress to Twitter publishing tool.
Author: Dale Mugford & Duane Storey (BraveNewCode)
Author URI: http://www.bravenewcode.com
Text Domain: wordtwit-pro
Domain Path: /lang
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html

# 'WordTwit' and 'WordTwit Pro' are unregistered trademarks of BraveNewCode Inc., 
# and cannot be re-used in conjuction with the GPL v2 usage of this software 
# under the license terms of the GPL v2 without the express prior written 
# permission of BraveNewCode Inc.
*/

// Should not have spaces in it, same as above
define( 'WORDTWIT_VERSION', '3.0b2' );

// Configuration
require_once( 'include/config.php' );

// Default settings
require_once( 'include/settings.php' );

// Helper classes
require_once( 'include/classes/array-iterator.php' );
require_once( 'include/classes/oauth.php' );
require_once( 'include/xml.php' );
require_once( 'include/classes/debug.php' );

// Administration Panel
require_once( 'admin/admin-panel.php' );
require_once( 'admin/template-tags/account.php' );

// Main WordTwit Class
require_once( 'include/classes/wordtwit-pro.php' );

$wordtwit_pro = &new WordTwitPro();

function wordtwit_create_object() {
	global $wordtwit_pro;
	
	// Initialize WordTwit, this is where the magic happens
	$wordtwit_pro->initialize();	
	
	require_once( 'include/globals.php' );		
}

add_action( 'plugins_loaded', 'wordtwit_create_object' );

