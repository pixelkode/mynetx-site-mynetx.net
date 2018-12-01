<?php

/* Administration panel bootstrap */
require_once( 'template-tags/tabs.php' );

add_action( 'admin_menu', 'wordtwit_admin_menu' );

function wordtwit_admin_menu() {
	$settings = wordtwit_get_settings();
	
	if ( wordtwit_user_can_add_account() ) {
			
		// Add the main plugin menu for WordTwit Pro 
		// TODO: Massage permissions
			
		if ( current_user_can( 'manage_options' ) ) {
			add_menu_page( 'WordTwit Pro', 'WordTwit Pro', 'edit_posts', __FILE__, '', WORDTWIT_URL . '/admin/images/wordtwit-admin-icon.png' );
			
			add_submenu_page( __FILE__, __( 'WordTwit Settings', 'wordtwit-pro' ), __( 'WordTwit Settings', 'wordtwit-pro' ), 'manage_options', __FILE__, 'wordtwit_admin_panel' );	
							
			add_submenu_page( __FILE__, __( 'Accounts', 'wordtwit-pro' ), __( 'Accounts', 'wordtwit-pro' ), 'edit_posts', 'wordtwit_account_configuration', 'wordtwit_admin_account_configuration' );	
			
			add_submenu_page( __FILE__, __( 'Tweet Log', 'wordtwit-pro' ), __( 'Tweet Log', 'wordtwit-pro' ), 'edit_posts', 'tweet_queue', 'wordtwit_admin_tweet_log' );		
		} else {
			add_menu_page( 'WordTwit Pro', 'WordTwit Pro', 'edit_posts', 'wordtwit_account_configuration', 'wordtwit_admin_account_configuration', WORDTWIT_URL . '/admin/images/wordtwit-admin-icon.png' );		
					
			add_submenu_page( 'wordtwit_account_configuration', __( 'Accounts', 'wordtwit-pro' ), __( 'Accounts', 'wordtwit-pro' ), 'edit_posts', 'wordtwit_account_configuration', 'wordtwit_admin_account_configuration' );	
			
			add_submenu_page( 'wordtwit_account_configuration', __( 'Tweet Log', 'wordtwit-pro' ), __( 'Tweet Log', 'wordtwit-pro' ), 'edit_posts', 'tweet_queue', 'wordtwit_admin_tweet_log' );				
		}
		
	}
}

function wordtwit_admin_panel() {	
	// Setup administration tabs
	wordtwit_setup_tabs();
	
	// Generate tabs	
	wordtwit_generate_tabs();
}

function wordtwit_admin_tweet_log() {
	include( WORDTWIT_DIR . '/admin/html/tweet-log.php' );
}

function wordtwit_admin_account_configuration() {
	include( WORDTWIT_DIR . '/admin/html/accounts.php' );	
}

//! Can be used to add a tab to the settings panel
function wordtwit_add_tab( $tab_name, $class_name, $settings, $custom_page = false ) {
	global $wordtwit_pro;
	
	$wordtwit_pro->tabs[ $tab_name ] = array(
		'page' => $custom_page,
		'settings' => $settings,
		'class_name' => $class_name
	);
}

function wordtwit_generate_tabs() {
	include( 'html/admin-form.php' );
}

function wordtwit_string_to_class( $string ) {
	return strtolower( str_replace( '--', '-', str_replace( '+', '', str_replace( ' ', '-', $string ) ) ) );
}	

function wordtwit_show_tab_settings() {
	include( 'html/tabs.php' );
}

function wordtwit_admin_get_languages() {
	$languages = array(
		'auto' => __( 'Auto-detect', 'wordtwit-pro' ),
		'da_DK' => 'Dansk',
		'de_DE' => 'Deutsch',
		'es_ES' => 'Español',
		'fr_FR' => 'Français',
		'it_IT' => 'Italiano',
		'ja_JP' => '日本語',
		'nl_NL' => 'Nederlands',
		'pt_PT' => 'Português',
		'ru_RU' => 'Русский язык',
		'sv_SE' => 'Svenska',
		'zh_CN' => '简体字',
		'zh_TW' => '簡體字'
	);	
	
	return apply_filters( 'wordtwit_admin_languages', $languages );
}

function wordtwit_save_reset_notice() {
	if ( isset( $_POST[ 'wordtwit-submit' ] ) ) {
		echo ( '<div class="saved">' );
		echo __( 'Settings saved!', "wordtwit-pro" );
		echo ('</div>');
	} elseif ( isset( $_POST[ 'wordtwit-submit-reset' ] ) ) {
		echo ( '<div class="reset">' );
		echo __( 'Defaults restored', "wordtwit-pro" );
		echo ( '</div>' );
	}
}

function wordtwit_get_transport_layers() {
	$transport_layers = array(	
		'default' => __( 'Default', 'wordtwit-pro' )	
	);
	
	if ( function_exists( 'curl_init' ) ) {
		$transport_layers[ 'curl' ] = __( 'Curl', 'wordtwit-pro' );	
	} 	
	
	return $transport_layers;
}

function wordtwit_get_shortener_list() {
	$shorteners = array(
		'wordpress' => 'WordPress',
		'bitly' => 'bit.ly',
		'isgd' => 'is.gd',
        'schiebde' => 'schieb.de',
		'tinyurl' => 'TinyURL',
		'yourls' => 'YOURLS'
	);
	
	return apply_filters( 'wordtwit_shortener_list', $shorteners );
}

function wordtwit_setup_general_tab() {
	$settings = wordtwit_get_settings();

	wordtwit_add_tab( __( 'General', 'wordtwit-pro' ), 'general', 
		array(
			__( 'Overview', 'wordtwit-pro' ) => array( 'dashboard', 
				array(
					array( 'section-start', 'twitboard', __( 'TwitBoard', "wordtwit-pro" ) ),
					array( 'dashboard' ),
					array( 'section-end' )
				)
			),
			__( 'Options', 'wordtwit-pro' ) => array( 'options',
				array(
					array( 'section-start', 'general-settings', __( 'General', 'wordtwit-pro' ) ),
					array( 'list', 'force_locale', __( 'Admin panel language', 'wordtwit-pro' ), '', wordtwit_admin_get_languages() ),
					array( 'checkbox', 'shorten_title', __( 'Shorten post titles to maintain tweet character limit', 'wordtwit-pro' ) ),
					array( 'checkbox', 'allow_users_to_add_accounts', __( 'Allow other WordPress users to add their own Twitter accounts', 'wordtwit-pro' ), '' ),					
					array( 
						'list',
						'minimum_user_capability_for_account_add', 
						__( 'WordPress users levels authorized to add Twitter accounts', 'wordtwit-pro' ),
						'',
						array(
							'edit_others_pages' => __( 'Editors', 'wordtwit-pro' ),
							'publish_posts' => __( 'Editors and Authors', 'wordtwit-pro' ),
							'edit_posts' => __( 'Editors, Authors and Contributors', 'wordtwit-pro' )
						)						
					),
					array(
						'list',
						'tweet_template',
						__( 'Individual tweet template', 'wordtwit-pro' ),
						'',
						wordtwit_get_tweet_templates()
					),			
					array( 'text', 'custom_tweet_template', __( 'Custom tweet template', 'wordtwit-pro' ), __( 'You can enter a custom tweet template to be used here.', 'wordtwit-pro' ) . ' ' . __( 'Valid tags are [post_type], [link], [title], [full_author], [short_author], and [hashtags].', 'wordtwit-pro' ) ),								
					array( 'section-end' ),
					array( 'section-start', 'shorteners', __( 'Shortening Method', 'wordtwit-pro' ) ),
					array( 
						'list',
						'url_shortener',
						__( 'URL Shortener', 'wordtwit-pro' ),
						__( 'Long URLs will automatically be shortened using the specified URL shortener', 'wordtwit-pro' ),
						wordtwit_get_shortener_list()
					),
					array( 'text', 'yourls_path', __( 'Full URL path to yourls-api.php', 'wordtwit-pro' ) ),
					array( 'text', 'yourls_signature', __( 'Authentication signature for YOURLS', 'wordtwit-pro' ), __( 'Can be found in the YOURLS administration panel.', 'wordtwit-pro' ) ),	
					array( 'text', 'bitly_username', __( 'Bit.ly username', 'wordtwit-pro' ) ),
					array( 'text', 'bitly_api_key', __( 'Bit.ly API key', 'wordtwit-pro' ) ),									
					array( 'section-end' ),
					array( 'section-start', 'options', __( 'Custom Application', 'wordtwit-pro' ) ),
					array( 'text', 'custom_consumer_key', __( 'Consumer Key', 'wordtwit-pro' ), sprintf( __( "You can also create %sa custom application via Twitter%s and have WordTwit tweet on from your application's namesake. Clear both of these fields to use the default Twitter OAuth configuration.", "wptouch-pro" ), '<a href="http://dev.twitter.com/apps">', '</a>' ) ),
					array( 'text', 'custom_consumer_secret', __( 'Consumer Secret', 'wordtwit-pro' ), '' ),
					array( 'section-end' )
				)
			),
			__( 'Tracking', 'wordtwit-pro' ) => array( 'tracking',
				array(
					array( 'section-start', 'tracking-options', __( 'Tracking', 'wordtwit-pro' ) ),
					array( 'checkbox', 'enable_utm', __( 'Add UTM tracking tags to Tweeted URLs', 'wordtwit-pro' ), __( "Adds UTM tags to the URLs created by WordTwit Pro. Requires a URL shortener other than 'WordPress' to be used.", "wordtwit-pro" )  ),
					array( 'text', 'utm_source', __( 'UTM source tag', 'wordtwit-pro' ) ),
					array( 'text', 'utm_medium', __( 'UTM medium tag', 'wordtwit-pro' ) ),
					array( 'text', 'utm_campaign', __( 'UTM campaign tag', 'wordtwit-pro' ) ),
					array( 'section-end' )
				)
			), /*
			__( 'Network', 'wordtwit-pro' ) => array( 'network',
				array(
					array( 'section-start', 'network-options', __( 'Network Options', 'wordtwit-pro' ) ),
					array( 'text', 'alternate_ip_address', __( 'Use alternate IP address', 'wordtwit-pro' ), '' ),
					array( 'list', 'transport_layer', __( 'Transport layer mechanism', 'wordtwit-pro' ), __( 'Can be used to force a specific connection to Twitter', 'wordtwit-pro' ), 
						wordtwit_get_transport_layers()
					),
					array( 'section-end' )				
				)
			), */
			__( 'Advanced', 'wordtwit-pro' ) => array( 'advanced',
				array(
					array( 'section-start', 'oauth', __( 'Twitter API Compliance', 'wordtwit-pro' ) ),
					array( 'text', 'oauth_time_offset', sprintf( __( 'Tweet time offset to correct server mismatch. %sEstimate offset.%s', 'wordtwit-pro' ), '<a href="#" id="estimate-offset">', '</a>' ), __( 'The Twitter API requires the time on your server to be correct.  You can use this field to add or subtract an amount of time. Number is in seconds only. e.g. 3600 (equals 1 hour)', 'wordtwit-pro' ) ),
					array( 'section-end' ),	
					array( 'section-start', 'custom-content', __( 'Custom Content', 'wordtwit-pro' ) ),
					array( 'text', 'custom_post_types', __( 'List of custom post types to Tweet', 'wordtwit-pro' ), __( 'Enter a comma-separated list of custom post types that will be Tweeted automatically.', 'wordtwit-pro' ) ),
					array( 'section-end' )
				)
			)
		)
	);	
	
	wordtwit_add_tab( __( 'Account + Licenses', 'wordtwit-pro' ), 'general',
		wordtwit_get_license_section_info()
	);		
}

function wordtwit_setup_tabs() {
	$settings = wordtwit_get_settings();
		
	wordtwit_setup_general_tab();
}

function wordtwit_get_license_section_info() {
	$info = array(
		__( 'Account', 'wordtwit-pro' ) => array ( 'bncid',
			array(
				array( 'section-start', 'account-information', __( 'Account Information', 'wordtwit-pro' ) ),
				array( 'copytext', 'bncid-info', __( 'Your Account E-Mail and License Key are required to enable site licenses for support and auto-upgrades with WordTwit Pro.', 'wordtwit-pro' ) ),
				array( 'text', 'bncid', __( 'Account E-Mail', 'wordtwit-pro' ) ),			
				array( 'key', 'license_key', __( 'License Key', 'wordtwit-pro' ) ),
				array( 'license-check', 'license-check' ),
				array( 'section-end' )
			)	
		)
	);
	
	if ( wordtwit_has_proper_auth() ) {
		$info[ __( 'Manage Licenses', 'wordtwit-pro' ) ] = array( 'manage-licenses-section',
			array(
				array( 'section-start', 'manage-license-info', __( 'Manage Licenses', 'wordtwit-pro' ) ),
				array( 'manage-licenses', 'manage-license' ),
				array( 'section-end' )
			)	
		);				
	}
	
	return $info;
}
