<?php

function wordtwit_has_license() {
	global $wordtwit_pro;
	
	$settings = $wordtwit_pro->get_settings();
	$settings->bncid_had_license = true;
	$settings->last_bncid_result = true;
	$settings->last_bncid_licenses = true;
	$wordtwit_pro->save_settings( $settings );		
	return $settings->last_bncid_result;
}

function wordtwit_clear_bnc_api_cache() {
	global $wordtwit_pro;
	
	$settings = $wordtwit_pro->get_settings();
	
	$settings->last_bncid_time = 0;
	$settings->last_bncid_result = false;			
	$settings->last_bncid_licenses = 0;	
	
	$wordtwit_pro->save_settings( $settings );		
	
	return $settings;
}

function wordtwit_was_username_invalid() {
	global $wordtwit_pro;
	
	return ( $wordtwit_pro->bnc_api->get_response_code() == 408 );
}

function wordtwit_user_has_no_license() {
	global $wordtwit_pro;
	
	return ( $wordtwit_pro->bnc_api->get_response_code() == 412 );	
}

function wordtwit_credentials_invalid() {
	global $wordtwit_pro;
	return $wordtwit_pro->bnc_api->credentials_invalid;
}

function wordtwit_api_server_down() {
	global $wordtwit_pro;
	
	$wordtwit_pro->bnc_api->verify_site_license();	
	return $wordtwit_pro->bnc_api->server_down;
}

function wordtwit_has_proper_auth() {
	wordtwit_has_license();
	
	$settings = wordtwit_get_settings();
	return $settings->last_bncid_licenses;
}

function wordtwit_is_upgrade_available() {
	global $wordtwit_pro;
	
	if ( defined( 'WORDTWIT_BETA' ) ) {
		$latest_info = $wordtwit_pro->bnc_api->get_product_version( true );
	} else {
		$latest_info = $wordtwit_pro->bnc_api->get_product_version();	
	}
    
	if ( $latest_info ) {
		return ( $latest_info['version'] != WORDTWIT_VERSION );
	} else {
		return false;	
	}
}

global $wordtwit_site_license;
global $wordtwit_site_license_info;
global $wordtwit_site_license_iterator;
$wordtwit_site_license_iterator = false;

function wordtwit_has_site_licenses() {
	global $wordtwit_pro;
	global $wordtwit_site_license_info;	
	global $wordtwit_site_license_iterator;
	
	if ( !$wordtwit_site_license_iterator ) {
		$wordtwit_site_license_info = $wordtwit_pro->bnc_api->user_list_licenses();
		$wordtwit_site_license_iterator = new WordTwitArrayIterator( $wordtwit_site_license_info['licenses'] );
	}	
	
	return $wordtwit_site_license_iterator->have_items();
}

function wordtwit_the_site_license() {
	global $wordtwit_site_license;
	global $wordtwit_site_license_iterator;
	
	$wordtwit_site_license = $wordtwit_site_license_iterator->the_item();
}

function wordtwit_the_site_licenses_remaining() {
	echo wordtwit_get_site_licenses_remaining();
}

function wordtwit_get_site_licenses_remaining() {
	global $wordtwit_site_license_info;	
		
	if ( $wordtwit_site_license_info && isset( $wordtwit_site_license_info['remaining'] ) ) {
		return $wordtwit_site_license_info['remaining'];
	}
	
	return 0;
}

function wordtwit_get_site_licenses_in_use() {
	global $wordtwit_site_license_info;	
	
	if ( $wordtwit_site_license_info && isset( $wordtwit_site_license_info['licenses'] ) && is_array( $wordtwit_site_license_info['licenses'] ) ) {
		return count( $wordtwit_site_license_info['remaining'] );
	}
	
	return 0;	
}

function wordtwit_the_site_license_name() {
	echo wordtwit_get_site_license_name();
}

function wordtwit_get_site_license_name() {
	global $wordtwit_site_license;
	return $wordtwit_site_license;
}

function wordtwit_is_licensed_site() {
	global $wordtwit_pro;
	return $wordtwit_pro->has_site_license();
}

function wordtwit_get_site_license_number() {
	global $wordtwit_site_license_iterator;
	return $wordtwit_site_license_iterator->current_position();
}

function wordtwit_can_delete_site_license() {
	return ( wordtwit_get_site_license_number() > 1 );	
}

$wordtwit_license_reset_info = false;

function wordtwit_can_do_license_reset() {
	global $wordtwit_license_reset_info;
	global $wordtwit_pro;
	
	$wordtwit_license_reset_info = $wordtwit_pro->bnc_api->get_license_reset_info( 'wordtwit-pro' );
	if ( isset( $wordtwit_license_reset_info['can_reset_licenses'] ) ) {
		return $wordtwit_license_reset_info['can_reset_licenses'];	
	} else {
		return false;	
	}
}

function wordtwit_get_license_reset_days() {
	global $wordtwit_license_reset_info;
	
	if ( $wordtwit_license_reset_info && isset( $wordtwit_license_reset_info['reset_duration_days'] ) ) {
		return $wordtwit_license_reset_info['reset_duration_days'];
	}	
	
	return 0;
}

function wordtwit_get_license_reset_days_until() {
	global $wordtwit_license_reset_info;
	
	if ( $wordtwit_license_reset_info && isset( $wordtwit_license_reset_info['can_reset_in'] ) ) {
		return $wordtwit_license_reset_info['can_reset_in'];
	}	
	
	return 0;	
}

