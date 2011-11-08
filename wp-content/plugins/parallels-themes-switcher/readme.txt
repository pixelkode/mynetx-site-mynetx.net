=== Parallels Themes Switcher ===
Contributors: xhtmlweaver
Author Homepage: http://www.xhtmlweaver.com
Tags: theme switcher, ajax theme switcher, plugin, theme editing, theme previewer, parallels, theme duplicating, theme develop, freelancer
Requires at least: 2.7
Tested up to: 3.1.3

This plugin allows you to modify/switch the current theme on the live site without interfering the current visitors.

== Description ==

In short, this plugin allows you to modify/switch the current theme with live site on the fly without messing up with your current visitors.
This plugin is perfect for Wordpress theme developers, freelancers as it allows you to edit the live theme without interfering the current visitors.
It offers following features:


* Ability to duplicate any current themes to the new theme for editing purpose.
* Selectively enabling the theme switcher on the frontend by enabling extra settings in the backend. (By role or by IP Addresses)
* Provides an Ajax theme switcher on the right top which selectively allows the visitor (by role or IP Addresses) to switch the theme.
* A transparent banner on the top to display current theme.
* Full Support is available at http://www.xhtmlweaver.com or wp-support@xhtmlweaver.com



== Installation ==
1. Download the plugin file and unzip it.
2. Put the theme directory into $WP_HOME/wp-content/plugins/ directory.
3. Make sure that the $WP_HOME/wp-content/themes directory is writable by the web server.
4. Activate' the plugin.
5. Settings under "Settings"-> "Parallels Theme Switcher".

== Screenshots ==

1. Ajax Theme Switcher
2. Current Theme Name Banner
3. Theme Switching
4. Plugin Settings
5. Duplicating current theme

== Frequently Asked Questions ==

= Why using this plugin I can modify the live theme on the fly?=
This theme allows you to duplicate current theme and modify the duplicated theme. Then the theme switcher will be available to the specified set of users in frontend. All other visitors cannot see the theme switcher and the wordpress instance will still use the current theme until you explicitly switch over to the newly fixed duplicated theme.


= Who can switch the theme in the frontend? =
Either admin user or the user whose  ip is listed in whitelisted IP addresses list.

= What does Whitelisted IP Addresses mean?
The ajax theme switcher will be available to these IP addresses which get whitelisted in backend.

= What should I do after the I finishing modifying the duplicated theme?=
Once you make sure you have fixed all issues with duplicated theme then just choose the duplicated theme.

= Why theme duplication feature doesn't work? =
Please make sure your theme directory is writable to web server user or the target directory doesn't exist in the system.

= What about bug report and support? =
Please drop us a line at wp-support@xhtmlweaver.com




== Upgrade Notice ==
N/A for this version


== Changelog ==
* code cleanup
* bug fixes
* test against the latest version of wordpress (3.1.3)
