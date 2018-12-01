<?php

class WordTwitSettings extends stdClass 
{	
};

class WordTwitDefaultSettings extends WordTwitSettings {
	function WordTwitDefaultSettings() {
		$this->test_setting = 'help';
		
		$this->accounts = array();
		
		$this->oauth_request_token = false;
		$this->oauth_request_token_secret = false;
		
		$this->custom_consumer_key = '';
		$this->custom_consumer_secret = '';
		
		$this->shorten_title = true;
		
		$this->contributors_can_add_accounts = true;
		$this->minimum_user_capability_for_account_add = 'publish_posts'; 

		$this->bncid = false;
		$this->license_key = false;
		
		$this->url_shortener = 'tinyurl';
		
		$this->bitly_username = false;
		$this->bitly_api_key = false;
		
		$this->yourls_path = '';
		$this->yourls_signature = '';
		
		$this->tweet_template = 'title_link_hashtags';
		$this->custom_tweet_template = sprintf( __( 'New [post_type]: %s', 'wordtwit-pro' ), '[title] - [link] [hashtags]' );
		
		$this->enable_utm = false;
		$this->utm_source = 'website';
		$this->utm_campaign = 'wordtwit';
		$this->utm_medium = 'web';
		
		$this->last_bncid_time = 0;
		
		$this->custom_post_types = '';
		$this->oauth_time_offset = 0;
		
		$this->force_locale = 'auto';
	}
};
