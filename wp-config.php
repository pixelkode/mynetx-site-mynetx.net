<?php
// ** MySQL settings ** //
define('DB_NAME', 'mynetxne_blog');    // The name of the database
define('DB_USER', 'mynetxne_blog');     // Your MySQL username
define('DB_PASSWORD', 'nQ2tmDmx@1!'); // ...and password
define('DB_HOST', 'pippa.srv.jublodns.com');    // 99% chance you won't need to change this value
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');


// Change each KEY to a different unique phrase.  You won't have to remember the phrases later,
// so make them long and complicated.  You can visit http://api.wordpress.org/secret-key/1.1/
// to get keys generated for you, or just make something up.  Each key should have a different phrase.
define('AUTH_KEY',        '~j>p34p*vV+^%<j1L)DDI3,CGf4ReO==4;Yicfn8w~~<csO^40a25E:,Z4y-IJTs');
define('SECURE_AUTH_KEY', 'Qe&j@!fVD*Qook,Cg{0-_#-<ER6D/)iz8Z#&cr:Ky/fhms,}L(VCy8?iQNe~*4-#');
define('LOGGED_IN_KEY',   'jgD(PC2H0G]*w9?L}AXRyfdH|&9GAoUBfiSg!0z>x-K4U`Oaj|qJ-dzV%?>q&C2|');
define('NONCE_KEY',       '3;@xUC3Ac,pO~Ubx20&u5w$uQ{D>/!;L0t$xp;8sYKvY`6A:r%3.8CHbZ}BI5B.P');

// You can have multiple installations in one database if you give each a unique prefix
$table_prefix  = 'mx_';   // Only numbers, letters, and underscores please!

// Various settings
session_set_cookie_params(86400, '/', '.mynetx.net');
define('COOKIE_DOMAIN', '.mynetx.net');
define('COOKIEPATH', '/');
define('SITECOOKIEPATH', '/');
define('ADMIN_COOKIE_PATH', '/');
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
/* That's all, stop editing! Happy blogging. */

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
?>