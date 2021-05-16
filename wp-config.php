<?php
define('WP_AUTO_UPDATE_CORE', 'minor');// This setting is required to make sure that WordPress updates can be properly managed in WordPress Toolkit. Remove this line if this WordPress website is not managed by WordPress Toolkit anymore.
// ** MySQL settings ** //
define('DB_NAME', 'mynetxne_blog');    // The name of the database
define('DB_USER', 'mynetxne_blog');     // Your MySQL username
define('DB_PASSWORD', 'nQ2tmDmx@1!'); // ...and password
define('DB_HOST', 'localhost');    // 99% chance you won't need to change this value
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');


// Change each KEY to a different unique phrase.  You won't have to remember the phrases later,
// so make them long and complicated.  You can visit http://api.wordpress.org/secret-key/1.1/
// to get keys generated for you, or just make something up.  Each key should have a different phrase.
define('AUTH_KEY', '6Vh/[zL+7/#aULdn16;i4m5xnkObt1:L@P[DR(#43WXf*/jx7m!B9868C5htCJtb');
define('SECURE_AUTH_KEY', '7O4c13*&0SCH26Hd2];6x|BD4@h4OyW1W/0%!vC6rR4:p2oPw7mV;b05y&!yBu6d');
define('LOGGED_IN_KEY', 'UjR9n|]:3Aj+QKA2[lHvPu4_4Tu71:NlJyQ#:9*W8tD5u_e_0-8si@GP8-9TeDo)');
define('NONCE_KEY', '674(7e4(@d72)0zC709usus0(~B5/yh(Z5-Mxh3-w3n-dj~6TlR*#YUnHaAD_]OR');

// You can have multiple installations in one database if you give each a unique prefix
$table_prefix  = 'mx_';   // Only numbers, letters, and underscores please!

// Various settings
session_set_cookie_params(86400, '/', '.mynetx.net');
define('COOKIE_DOMAIN', '.mynetx.net');
define('COOKIEPATH', '/');
define('SITECOOKIEPATH', '/');
define('ADMIN_COOKIE_PATH', '/');
if (defined('WP_PLUGIN_URL'))
define('PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL));

 //Added by WP-Cache Manager
define('WP_ALLOW_REPAIR', true);

//define('ICL_DEBUG_DEVELOPMENT', true);
define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
define('ICL_DONT_LOAD_LANGUAGES_JS', true);

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', 'en_US');

define('WP_MEMORY_LIMIT', '128MB'); 
define('AUTH_SALT', 'H9;au63y[6696ulhm_t93-i78630_8T47&IOZE3[:1nT;l6/;a;%I6SR2/szz:y7');
define('SECURE_AUTH_SALT', 'cK*@99RQ9F0t:48~~5vs2aM~s3W7I:02x7hIjeX6m3e4+6xcX7J#z70Hej:W7P8G');
define('LOGGED_IN_SALT', 'ja121W-jZ@kqrCDz~4h;_10XN|um5N5k(+y2gne1(+jGhF8pZoEN*9P75(70t00G');
define('NONCE_SALT', '/@_~4SziWdk@xe2ug)Ea;@;Z3E(h7E8h|u3F(uj3j&oa4WLZ~x2xQ63An6JT%AZv');
/* That's all, stop editing! Happy blogging. */

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
?>
