<?php

define( 'WORDTWIT_TWEET_UNPUBLISHED', 0 );
define( 'WORDTWIT_TWEET_SCHEDULED', 1 );
define( 'WORDTWIT_TWEET_PUBLISHED', 2 );
define( 'WORDTWIT_TWEET_IS_OLD', 3 );

define( 'WORDTWIT_TWEET_AUTOMATIC', 0 );
define( 'WORDTWIT_TWEET_MANUAL', 1 );


global $wordtwit_pro;
	
function wordtwit_get_settings() {
	global $wordtwit_pro;

	return $wordtwit_pro->get_settings();	
}

function wordtwit_user_can_make_global() {
	return ( current_user_can( 'manage_options' ) );	
}

function wordtwit_user_can_add_account() {
	if ( current_user_can( 'manage_options' ) ) {
		return true;	
	} else {
		// Check how many local accounts are allowed
		return current_user_can( 'edit_posts' );	
	}
}

function wordtwit_get_accounts() {
	$settings = wordtwit_get_settings();
	
	$accounts = array();
	if ( isset( $settings->accounts ) ) {
		$local = array();
		$global = array();
		foreach( $settings->accounts as $key => $value ) {
			if ( wordtwit_current_user_can_view_account( $value ) ) {
				if ( $value->is_global ) {
					$global[ $key ] = $value;
				} else {
					$local[ $key ] = $value;
				}	
			}
		}	
	
		$accounts = array_merge( $global, $local );
	} 
	
	return apply_filters( 'wordtwit_accounts', $accounts );
}

function wordtwit_current_user_can_view_account( $account = false ) {
	global $wordtwit_account;
	
	if ( !$account ) {
		$account = $wordtwit_account;
	}
	
	if ( current_user_can( 'manage_options' ) || $account->is_global ) {
		return true;	
	} else {
		global $current_user;
		get_currentuserinfo();		
		
		return ( $current_user->ID == $account->owner );		
	}
}

function wordtwit_current_user_can_delete_account( $account = false ) {
	global $wordtwit_account;
	
	if ( !$account ) {
		$account = $wordtwit_account;
	}
	
	if ( current_user_can( 'manage_options' ) ) {
		return true;	
	} else {
		global $current_user;
		get_currentuserinfo();		
		
		return ( $current_user->ID == $account->owner );		
	}
}

function wordtwit_current_user_can_modify_account( $account = false ) {
	global $wordtwit_account;
	
	if ( !$account ) {
		$account = $wordtwit_account;
	}
	
	if ( $account->owner ) {
		// Local account
		global $current_user;
		get_currentuserinfo();	
				
		return current_user_can( 'manage_options' ) && ( $current_user->ID == $account->owner );
	} else {
		// Global account
		return current_user_can( 'manage_options' );
	}
}


function wordtwit_get_tweet_templates() {
	$tweet_templates = array(
		'title_link_hashtags' => '[title] - [link] [hashtags]',
		'title_author_short_link_hashtags' => __( '[title] by [short_author] - [link] [hashtags]', 'wordtwit-pro' ),
		'title_author_full_link_hashtags' => __( '[title] by [full_author] - [link] [hashtags]', 'wordtwit-pro' ),
		'post_type_title_link_hashtags' => __( 'New [post_type]: [title] - [link] [hashtags]', 'wordtwit-pro' ),
		'post_type_title_short_author_link_hashtags' => __( 'New [post_type]: [title] by [short_author] - [link] [hashtags]', 'wordtwit-pro' ),
		'post_type_title_full_author_link_hashtags' => __( 'New [post_type]: [title] by [full_author] - [link] [hashtags]', 'wordtwit-pro' ),		
		'custom' => __( 'Custom', 'wordtwit-pro' )
	);	
	
	return apply_filters( 'wordtwit_tweet_templates', $tweet_templates );
}

function wordtwit_get_short_post_link( $num = 1 ) {
	$settings = wordtwit_get_settings();
	
	$link = get_permalink();
	
	if ( $num > 1 ) {
		$link = add_query_arg( 'wt', $num, $link );
	}
	
	if ( $settings->enable_utm ) {
		$link = add_query_arg( array( 'utm_source' => $settings->utm_source, 'utm_campaign' => $settings->utm_campaign, 'utm_medium' => $settings->utm_medium ), $link );	
	}

	switch( $settings->url_shortener ) {
		case 'bitly':		
			require_once( WORDTWIT_DIR . '/include/shorteners/bitly.php' );
			$tinyurl = new WordTwitBitlyShortener( $settings->bitly_username, $settings->bitly_api_key );
			$link = $tinyurl->shorten( $link );
			break;
		case 'owlly':
			break;
        case 'isgd':
            require_once( WORDTWIT_DIR . '/include/shorteners/isgd.php' );
            $isgd_link = new WordTwitIsgdShortener;
            $link = $isgd_link->shorten( $link );           
            break;
        case 'schiebde':
            require_once( WORDTWIT_DIR . '/include/shorteners/schiebde.php' );
            $schiebde_link = new WordTwitSchiebdeShortener;
            $link = $schiebde_link->shorten( $link );           
            break;
		case 'tinyurl':
			require_once( WORDTWIT_DIR . '/include/shorteners/tinyurl.php' );
			$tinyurl = new WordTwitTinyUrlShortener;
			$link = $tinyurl->shorten( $link );
			break;
		case 'yourls':
			require_once( WORDTWIT_DIR . '/include/shorteners/yourls.php' );
			$yourls_link = new WordTwitYourlsShortener( $settings->yourls_path, $settings->yourls_signature );
			$link = $yourls_link->shorten( $link );		
			break;
		case 'wordpress':
			$link = rtrim( get_bloginfo( 'home' ), '/' ) . '?p=' . get_the_ID();
			
			if ( $num > 1 ) {
				$link = add_query_arg( 'wt', $num, $link );	
			}
			
			break;
	}
	
	return apply_filters( 'wordtwit_short_port_link', $link );
}

function wordtwit_get_bloginfo( $param ) {
	global $wpdb;
	$setting = false;
	
	switch( $param ) {
		case 'published_tweets':
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT count(*) AS c FROM " . $wpdb->posts . " WHERE post_status = 'publish' AND post_type = 'tweet'" ) );
			if ( $result ) {
				$setting = $result->c;	
			}
			break;
		case 'scheduled_tweets':
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT count(*) AS c FROM " . $wpdb->posts . " WHERE post_status = 'future' AND post_type = 'tweet'" ) );
			if ( $result ) {
				$setting = $result->c;	
			}		
			break;
		case 'total_accounts':
			$settings = wordtwit_get_settings();
			if ( $settings ) {
				if ( is_array( $settings->accounts ) ) {
					return count( $settings->accounts );
				} else {
					return 0;
				}		
			}
			break;
		case 'licenses_remaining':
			// todo: hook this up
			global $wordtwit_pro;
			$wordtwit_pro->setup_bnc_api();
			$licenses = $wordtwit_pro->bnc_api->user_list_licenses( 'wordtwit-pro' );
			if ( $licenses ) {
				$setting = $licenses['remaining'];	
			} else {
				$setting = 0;	
			}
			break;
	}
	
	return $setting;	
}

function wordtwit_the_bloginfo( $param ) {
	echo wordtwit_get_bloginfo( $param );	
}

function wordtwit_is_twitter_api_up() {
	$twitter_api = new WordTwitOAuth();
	
	$result = $twitter_api->get_user_info( 'bravenewcode' );	
	return ( $result && isset( $result['user'] ) );
}

function wordtwit_setup_bnc_api() {
	require_once( WORDTWIT_DIR . '/include/bnc-api-functions.php' ); 
	
	global $wordtwit_pro;
	$wordtwit_pro->setup_bnc_api();
}

	
function wordtwit_get_custom_post_types() {
	$settings = wordtwit_get_settings();
	$custom_types = array();	

	if ( strlen( $settings->custom_post_types ) ) {
		$custom_post_types = explode( "," , $settings->custom_post_types );	
		if ( $custom_post_types ) {
			foreach( $custom_post_types as $post_type ) {
				$custom_types[] = trim( $post_type );
			}	
		}	
	}
	
	return apply_filters( 'wordtwit_custom_post_types', $custom_types );	
}
