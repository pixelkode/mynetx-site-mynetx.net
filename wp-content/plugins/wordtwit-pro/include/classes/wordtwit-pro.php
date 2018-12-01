<?php

class WordTwitPro {
	var $tabs;
	var $settings;
	
	var $post;
	var $get;
	
	var $oauth;
	var $bnc_api;
	
	var $transient_set;
	
	function WordTwitPro() {
		$this->tabs = array();
		$this->settings = array();

		// Fixed up get and post variables
		$this->get  = array();			
		$this->post = array();	
		
		$this->bnc_api = false;
		$this->transient_set = false;
		$this->locale = false;
	}
	
	function show_post_box() {
		include( WORDTWIT_DIR . '/include/html/post-box.php' );
	}

	function construct_admin_menu() {
		if ( function_exists( 'add_meta_box' ) ) {
			$post_types = array_merge( array( 'post' ), wordtwit_get_custom_post_types() );
			foreach( $post_types as $post_type ) {
				add_meta_box( 'wordtwit-box', __( 'WordTwit Pro', 'wordtwit-pro' ), array( &$this, 'show_post_box' ), $post_type, 'side' );				
			}
		}
	}
	
	function redirect_to_options_page() {
		header( 'Location: ' . admin_url( 'admin.php?page=wordtwit-pro/admin/admin-panel.php' ) );
		die;
	}
	
	function redirect_to_account_page() {
		header( 'Location: ' . admin_url( 'admin.php?page=wordtwit_account_configuration' ) );
		die;		
	}
	
	function initialize() {
		add_action( 'init', array( &$this, 'cleanup_post_and_get' ) );
		add_action( 'init', array( &$this, 'setup_languages' ) );
		add_action( 'init', array( &$this, 'check_directories' ) );
		add_action( 'init', array( &$this, 'process_submitted_settings' ) );
		add_action( 'init', array( &$this, 'setup_custom_taxonomies' ) );
		
		add_action( 'admin_head', array( &$this, 'wordtwit_admin_head' ) );
		add_action( 'admin_init', array( &$this, 'wordtwit_admin_init' ) );
		add_action( 'admin_init', array( &$this, 'check_for_new_account' ) );
		add_action( 'admin_init', array( &$this, 'check_for_account_actions' ) );
		add_action( 'admin_init', array( &$this, 'check_for_tweet_log_actions' ) );
		add_action( 'admin_init', array( &$this, 'check_for_post_box_actions' ) );
		add_action( 'admin_menu', array( &$this, 'construct_admin_menu' ) );
		add_action( 'admin_footer', array( &$this, 'wordtwit_admin_footer' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'wordtwit_admin_js' ) );
		
		add_action( 'publish_post', array( &$this, 'post_now_published' ) );
		add_action( 'publish_tweet', array( &$this, 'publish_tweet' ) );
		
		add_action( 'wordtwit_settings_loaded', array( &$this, 'post_settings_setup' ) );		
		
		$this->oauth = new WordTwitOAuth();	
		
		add_action( 'wp_ajax_wordtwit_ajax', array( &$this, 'admin_ajax_handler' ) );	
	}
	
	function post_settings_setup() {
		// setup custom post types
		$custom_post_types = wordtwit_get_custom_post_types();
		
		if ( count( $custom_post_types ) ) {
			foreach( $custom_post_types as $post_type ) {
				$cleaned_up_post_type = trim( $post_type );
				add_action( 'publish_' . $cleaned_up_post_type, array( &$this, 'post_now_published' ) );	
			}	
		}
				
		$settings = wordtwit_get_settings();
		// adjust oauth time if required
		if ( $settings->oauth_time_offset != 0 ) {
			$this->oauth->set_oauth_time_offset( $settings->oauth_time_offset );
		}		
		
		if ( strlen( $settings->custom_consumer_key ) && strlen( $settings->custom_consumer_secret ) ) {
			$this->oauth->set_custom_key_and_secret( $settings->custom_consumer_key, $settings->custom_consumer_secret );	
		}
		
	}
	
	function publish_tweet( $tweet_id ) {
		// used to publish tweets
		$post = get_post( $tweet_id );
		if ( $post ) {
			$associated_post_id = get_post_meta( $tweet_id, 'wordtwit_real_post', true );
			if ( $associated_post_id ) {
				require_once( WORDTWIT_DIR . '/include/post-box-functions.php' );

				$account = get_post_meta( $tweet_id, 'wordtwit_account', true );
				if ( $account ) {	
					$settings = wordtwit_get_settings();

					if ( isset( $settings->accounts[ $account ] ) ) {
						$account_info = $settings->accounts[ $account ];
						
						$modified_title = html_entity_decode( $post->post_title );
						$this->oauth->update_status( $account_info->token, $account_info->secret, $modified_title );
						
						$tweet_result = new stdClass;
						$tweet_result->code = $this->oauth->get_response_code();
						$tweet_result->error = $this->oauth->get_error_message();
						
						update_post_meta( $tweet_id, 'wordtwit_result', $tweet_result );
						
						// Check for a valid result code
						if ( $tweet_result->code != 200 ) {
							// update the post status to ERROR
							global $wpdb;
							$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->posts . " SET post_content = 'error' WHERE ID = %d", $tweet_id ) );
						}
					}
				}					
			}
		}
	}
	
	function add_tweet_log_post( $post_id, $account, $offset = 0 ) {
		require_once( WORDTWIT_DIR . '/include/post-box-functions.php' );
		
		$tweet_info = wordtwit_get_saved_tweet_info( $post_id );
		$settings = wordtwit_get_settings();
		
		if ( $post_id && $tweet_info && $account && isset( $settings->accounts[ $account ] ) ) {		
			$tweet_text = wordtwit_get_post_tweet( $post_id, $tweet_info->tweet_counter );
			
			$new_post = array(
				'post_title' => $tweet_text,
				'post_content' => 'ok',
				'post_author' => 1,
				'post_type' => 'tweet'
			);

			$should_publish = false;
			
			if ( $offset ) {
				$new_post[ 'post_status' ] = 'future';	
				$new_post[ 'post_date' ] = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) + $offset );
			} else {
				$new_post[ 'post_status' ] = 'draft';	
				$should_publish = true;
			}				
			
			$tweet_id = wp_insert_post( $new_post );
			if ( $tweet_id ) {
				add_post_meta( $tweet_id, 'wordtwit_real_post', $post_id );
				add_post_meta( $tweet_id, 'wordtwit_account', $account );
				
				$tweet_info->tweet_log_ids[] = $tweet_id;
				
				// We have to delay publishing the post, otherwise the meta values happen after the post is published
				// which results in the tweet not going out
				if ( $should_publish ) {
					wp_publish_post( $tweet_id );
				}
				
				$tweet_info->tweet_counter = $tweet_info->tweet_counter + 1;
			}
			
			wordtwit_save_tweet_info( $tweet_info, $post_id );	
		}	
	} 
	
	function post_now_published( $post_id, $force_it = false ) {
		require_once( WORDTWIT_DIR . '/include/post-box-functions.php' );
		
		$tweet_info = wordtwit_get_saved_tweet_info( $post_id );
		if ( $tweet_info ) {
			if ( $tweet_info->status == WORDTWIT_TWEET_PUBLISHED ) {
				// Don't tweet posts that have already been tweeted
				return;	
			}
			
			if ( $tweet_info->status == WORDTWIT_TWEET_IS_OLD && !$force_it ) {
				// Don't allow publishing posts that are old
				return;
			}
			
			for( $i = 1; $i <= $tweet_info->tweet_times; $i++ ) {
				$post_offset = ( $tweet_info->delay + $tweet_info->separation*( $i - 1 ) ) * 60;
				
				foreach( $tweet_info->accounts as $account ) {
					$this->add_tweet_log_post( $post_id, $account, $post_offset );
				}
			}
	
			// change status to published, reload tweet_info since it has possibly been saved in other functions
			$tweet_info = wordtwit_get_saved_tweet_info( $post_id );
			$tweet_info->status = WORDTWIT_TWEET_PUBLISHED;			
			
			wordtwit_save_tweet_info( $tweet_info, $post_id );
		}
	}
	
	function admin_ajax_handler() {
		$this->cleanup_post_and_get();
		$this->setup_bnc_api();
		
		if ( current_user_can( 'manage_options' ) ) {
			// Check security nonce
			$wordtwit_nonce = $this->post['wordtwit_nonce'];
			
			if ( !wp_verify_nonce( $wordtwit_nonce, 'wordtwit_admin' ) ) {	
				_e( 'Failed security checked', 'wordtwit-pro' );
				exit;	
			}

			$wordtwit_ajax_action = $this->post['wordtwit_action'];
			switch( $wordtwit_ajax_action ) {
				case 'manage-licenses':
					include( WORDTWIT_DIR . '/admin/ajax/license-area.php' );
					break;
				case 'activate-site-license':
					$this->bnc_api->user_add_license();
					wordtwit_clear_bnc_api_cache();
					break;
				case 'deactivate-site-license':
					$this->bnc_api->user_remove_license( $this->post[ 'site' ] );
					wordtwit_clear_bnc_api_cache();
					break;				
				case 'wordtwit-news':
					include( WORDTWIT_DIR . '/admin/ajax/news.php' );
					break;
				case 'dashboard-ajax':
					include( WORDTWIT_DIR . '/admin/ajax/dashboard-info.php' );
					break;
				case 'save-post-data':
					$this->cleanup_post_and_get();
				
					require_once( WORDTWIT_DIR . '/include/post-box-functions.php' );
					$tweet_info = wordtwit_get_saved_tweet_info( $this->post['post'] );
					if ( $this->post['manual'] == 1 ) {
						$tweet_info->manual = true;	
						$tweet_info->text = $this->post['tweet_text'];
					} else {
						$tweet_info->manual = false;
					}
					
					$tweet_info->tweet_times = $this->post['tweet_times'];
					
					if ( $tweet_info->tweet_times > 1 ) {
						$tweet_info->separation = $this->post['tweet_sep_hour']*60 + $this->post['tweet_sep_min'];
					} else {
						$tweet_info->separation = 60;	
					}
					
					$tweet_info->delay = $this->post['tweet_delay'];
					$tweet_info->enabled = $this->post['enabled'];
					
					$tweet_info->accounts = array();
					$tweet_info->hash_tags = array();
					foreach( $this->post as $key => $value ) {
						if ( preg_match( '#account_(.*)#i', $key, $matches ) ) {
							$tweet_info->accounts[] = $matches[1];
						}	
						
						if ( preg_match( '#hash_(.*)#i', $key, $matches ) ) {
							$tweet_info->hash_tags[] = $this->post[ $matches[0] ];
						}	
					}				
										
					update_post_meta( $this->post['post'], 'wordtwit_post_info', $tweet_info );
					echo wordtwit_get_post_tweet( $this->post['post'] );
					break;
				case 'update-post-data':
					require_once( WORDTWIT_DIR . '/include/post-box-functions.php' );
					echo wordtwit_get_post_tweet( $this->post['post'] );
					break;
				case 'estimate-offset':	
					if ( !class_exists( 'WP_Http' ) ) {
						include_once( ABSPATH . WPINC. '/class-http.php' );
					}
					
					$http = new WP_Http;
					$url = 'http://code.bravenewcode.com/time_offset/?unixtime=' . time();
					$response = $http->request( $url );
					if ( !is_wp_error( $response ) ) {
						if ( $response['response']['code'] == '200' ) {
							echo $response['body'];
						}
					}
					break;
				default:
					break;
			}	
		}	
		die;		
	}
	
	
	function get_latest_news( $quantity = 8 ) {
		if ( !function_exists( 'fetch_feed' ) ) {
			include_once( ABSPATH . WPINC . '/feed.php' );
		}
		
		$rss = fetch_feed( 'http://www.bravenewcode.com/category/wordtwit/feed' );
		if ( !is_wp_error( $rss ) ) {
			$max_items = $rss->get_item_quantity( $quantity ); 
			$rss_items = $rss->get_items( 0, $max_items ); 
			
			return $rss_items;	
		} else {		
			return false;
		}
	}	
	
	function setup_custom_taxonomies() {
		register_post_type(
			'tweet',
			array(
				'label' => __( 'Tweet Log', 'wordtwit-pro' ),
				'name' => 'tweet',
				'public' => false
			)
		);	
	}
	
	
	function cleanup_post_and_get() {		
		if ( count( $_GET ) ) {
			foreach( $_GET as $key => $value ) {
				if ( get_magic_quotes_gpc() ) {
					$this->get[ $key ] = @stripslashes( $value );	
				} else {
					$this->get[ $key ] = $value;
				}
			}	
		}	
		
		if ( count( $_POST ) ) {
			foreach( $_POST as $key => $value ) {
				if ( get_magic_quotes_gpc() ) {
					$this->post[ $key ] = @stripslashes( $value );	
				} else {
					$this->post[ $key ] = $value;	
				}
			}	
		}	
	}
	
    function check_for_update() {
    	$this->setup_bnc_api();
    	  	
    	if ( defined( 'WORDTWIT_PRO_BETA' ) ) {
 			$plugin_name = "wordtwit-pro-beta/wordtwit-pro.php";
 			$latest_info = $this->bnc_api->get_product_version( 'wordtwit-pro', true );
    	} else {
    		$plugin_name = "wordtwit-pro/wordtwit-pro.php";
    		$latest_info = $this->bnc_api->get_product_version( 'wordtwit-pro' );
    	}
    	
        // Check for WordPress 3.0 function
		if ( function_exists( 'is_super_admin' ) ) {
			$option = get_site_transient( 'update_plugins' );
		} else {
			$option = function_exists( 'get_transient' ) ? get_transient( 'update_plugins' ) : get_option( 'update_plugins' );
		}
    	
    	if ( $latest_info && $latest_info['version'] != WORDTWIT_VERSION && isset( $latest_info['upgrade_url'] ) ) {    	  	
	        $wordtwit_option = $option->response[ $plugin_name ];
	
	        if ( empty( $wordtwit_option ) ) {
	            $option->response[ $plugin_name ] = new stdClass();
	        }
	
			$option->response[ $plugin_name ]->url = 'http://www.bravenewcode.com/store/plugins/wordtwit-pro';
			$option->response[ $plugin_name ]->package = $latest_info['upgrade_url'];
			$option->response[ $plugin_name ]->new_version = $latest_info['version'];
			$option->response[ $plugin_name ]->id = '0';
			
			if ( WORDTWIT_PRO_BETA ) {
				$option->response[ $plugin_name ]->slug = 'wordtwit-pro-beta';	
			} else {
				$option->response[ $plugin_name ]->slug = 'wordtwit-pro';
			}

	        $this->latest_version_info = $latest_info;
    	} else {
    		unset( $option->response[ $plugin_name ] );	
    	}
    		
        if ( !$this->transient_set ) {      
        	// WordPress 3.0 changed some stuff, so we check for a WP 3.0 function
			if ( function_exists( 'is_super_admin' ) ) {
				$this->transient_set = true; 
				set_site_transient( 'update_plugins', $option );
			} else {
				if ( function_exists( 'set_transient' ) ) {
					$this->transient_set = true;
					set_transient( 'update_plugins', $option );
				}
			}
        }
        	
    }	
	
	function check_for_post_box_actions() {
		if ( $this->is_admin_section() && isset( $this->get['wordtwit_post_action'] ) ) {
			if ( $this->verify_get_nonce() ) {
				switch( $this->get['wordtwit_post_action'] ) {
					case 'retweet':
						require_once( WORDTWIT_DIR . '/include/post-box-functions.php' );
						$tweet_info = wordtwit_get_saved_tweet_info( $this->get['post'] );
						if ( $tweet_info ) {
							foreach( $tweet_info->accounts as $account ) {
								$this->add_tweet_log_post( $this->get['post'], $account );
							}
						}
						break;	
					case 'publish_now':
						$this->post_now_published( $this->get['post'], true );
						break;
				}
			}
			
			header( 'Location: ' . add_query_arg( array( 'wordtwit_post_action' => null, 'wordtwit_nonce' => null ) ) );
			die;
		}		
	}
	
	function check_for_tweet_log_actions() {
		if ( $this->is_admin_section() && isset( $this->get['wordtwit_tweet_action'] ) ) {
			if ( $this->verify_get_nonce() ) {
				$settings = $this->get_settings();
				
				$wordtwit_account_action = $this->get['wordtwit_tweet_action'];
				
				switch( $wordtwit_account_action ) {
					case 'delete':
						wp_delete_post( $this->get[ 'log_id'], true );
						break;
					case 'tweet_now':
						global $wpdb;
						$wpdb->update( 
							$wpdb->posts, 
							array( 
								'post_date_gmt' => time() - 1, 
								'post_date' => date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) ) 
							), 
							array( 'ID' => $this->get[ 'log_id' ] ) 
						);
								
						check_and_publish_future_post( $this->get[ 'log_id' ] );
						break;	
					case 'retweet':
						$associated_post_id = get_post_meta( $this->get[ 'log_id' ], 'wordtwit_real_post', true );
						if ( $associated_post_id ) {
							$this->add_tweet_log_post( $associated_post_id, $this->get['wordtwit_account' ] );
						}
						break;
				}			
			}
					
			$location = add_query_arg( array( 'log_id' => null, 'wordtwit_nonce' => null, 'wordtwit_tweet_action' => null, 'wordtwit_account' => null ) );
			header( 'Location: ' . $location );
			die; 
		}		
	}
	
	function check_for_account_actions() {
		if ( $this->is_admin_section() && isset( $this->get['wordtwit_action'] ) ) {
			if ( $this->verify_get_nonce() ) {
				$settings = $this->get_settings();
				
				$wordtwit_account_action = $this->get['wordtwit_action'];
				switch( $wordtwit_account_action ) {
					case 'delete_account':
						$account = $settings->accounts[ $this->get['wordtwit_user'] ];
						
						if ( $account && wordtwit_current_user_can_delete_account( $account ) ) {			
							unset( $settings->accounts[ $this->get['wordtwit_user'] ] );
								
							$this->save_settings( $settings );
						}
						break;
					case 'refresh_account':
						$account = $settings->accounts[ $this->get['wordtwit_user'] ];
						if ( wordtwit_current_user_can_delete_account( $account ) ) {
							if ( $account ) {
								$user_id = $account->user_id;
								$updated_user_info = $this->oauth->get_user_info( $user_id );	
								if( $updated_user_info ) {
									$account = $this->update_twitter_info( $updated_user_info, $account	);
									
									$settings->accounts[ $this->get['wordtwit_user'] ] = $account;
									$this->save_settings( $settings );
								}
							}
						}
						break;
					case 'change_account_type':
						$account = $settings->accounts[ $this->get['wordtwit_user'] ];
						if ( $account && wordtwit_current_user_can_modify_account( $account ) ) {
							switch( $this->get['wordtwit_account_type'] ) {
								case 'local':
									$account->is_global = false;
									
									global $current_user;
									get_currentuserinfo();
									
									$account->owner = $current_user->ID;									
									break;
								case 'global':
									$account->owner = 0;
									$account->is_global = true;	
									break;	
							}
											
							$settings->accounts[ $this->get['wordtwit_user'] ] = $account;
							
							$this->save_settings( $settings );
						}				
						break;
				}
			}
			
			$this->redirect_to_account_page();
		}
	}
	
	function check_for_new_account() {
		if ( $this->is_admin_section() && isset( $_GET['wordtwit_pro_oauth'] ) ) {	
			$settings = $this->get_settings();
			
			if ( $settings->oauth_request_token && $settings->oauth_request_token_secret ) {
			
				$access_token = $this->oauth->get_access_token( 
					$settings->oauth_request_token, 
					$settings->oauth_request_token_secret, 
					$this->get['oauth_verifier']
				);
				
				if ( $access_token && !isset( $settings->accounts[ $access_token['screen_name'] ] ) ) {
					$account = new stdClass;
					$account->token = $access_token['oauth_token'];
					$account->secret = $access_token['oauth_token_secret'];
					$account->user_id = $access_token['user_id'];
					$account->screen_name = $access_token['screen_name'];
					$account->is_default = true;
					
					if ( current_user_can( 'manage_options' ) ) {
						$account->owner = 0;	
					} else {
						global $current_user;
						get_currentuserinfo();
						
						$account->owner = $current_user->ID;
					}
					
					if ( wordtwit_user_can_make_global() ) {
						$account->is_global = true;
					} else {
						$account->is_global = false;
					}
					
					$user_info = $this->oauth->get_user_info( $account->user_id );
					if ( $user_info ) {
						$account = $this->update_twitter_info( $user_info, $account );
											
						$settings->accounts[ $account->screen_name ] = $account;	
					}
					
					ksort( $settings->accounts );
					
					$this->save_settings( $settings );
				}
			}
						
			$this->redirect_to_account_page();
		}
	}
	
	function update_twitter_info( $user_info, $account ) {
		$account->id = $user_info['user']['id'];
		$account->profile_image_url = $user_info['user']['profile_image_url'];
		$account->location = $user_info['user']['location'];
		$account->utc_offset = $user_info['user']['utc_offset'];
		$account->description = $user_info['user']['description'];	
		$account->followers_count = $user_info['user']['followers_count'];
		$account->name = $user_info['user']['name'];
		$account->url = $user_info['user']['url'];
		$account->statuses_count = $user_info['user']['statuses_count'];		
		
		return $account;
	}
	
	function refresh_twitter_user_info( $user_id ) {
		$settings = wordtwit_get_settings();
		
		$user_info = $this->oauth->get_user_info( $user_id );
		if ( $user_info && isset( $settings->accounts ) && isset( $settings->accounts[ $user_info['user']['screen_name'] ] ) ) {
			$settings->accounts[ $user_info['user']['screen_name'] ] = $this->update_twitter_info( $user_info, $settings->accounts[ $user_info['user']['screen_name'] ] );
			
			$this->save_settings( $settings );
		}
	}
	
	function setup_languages() {		
		$current_locale = get_locale();
		
		// Check for language override
		$settings = $this->get_settings();
		if ( $settings->force_locale != 'auto' ) {
			$current_locale = $settings->force_locale;
		}
		
		if ( !empty( $current_locale ) ) {
			$current_locale = apply_filters( 'wordtwit_language', $current_locale );
			
			$use_lang_file = false;
			$custom_lang_file = WORDTWIT_CUSTOM_LANG_DIRECTORY . '/' . $current_locale . '.mo';
			
			if ( file_exists( $custom_lang_file ) && is_readable( $custom_lang_file ) ) {
				$use_lang_file = $custom_lang_file;
			} else {
				$lang_file = WORDTWIT_DIR . '/lang/' . $current_locale . '.mo';
				if ( file_exists( $lang_file ) && is_readable( $lang_file ) ) {
					$use_lang_file = $lang_file;
				}
			}
					
			if ( $use_lang_file ) {
				load_textdomain( 'wordtwit-pro', $use_lang_file );	
			}
			
			$this->locale = $current_locale;
			
			do_action( 'wordtwit_language_loaded', $this->locale );
		}
	}	
	
	function create_directory_if_not_exist( $dir ) {
		if ( !file_exists( $dir ) ) {
			// Try and make the directory
			if ( !wp_mkdir_p( $dir ) ) {
				$this->directory_creation_failure = true;
			}	
		}	
	}		
	
	function check_directories() {
		$this->create_directory_if_not_exist( WORDTWIT_CUSTOM_DIRECTORY );
		$this->create_directory_if_not_exist( WORDTWIT_CUSTOM_LANG_DIRECTORY );
	}			
	
	function is_admin_section() {
		return ( 
			is_admin() && 
			( 
				strpos( $_SERVER['REQUEST_URI'], 'wordtwit-pro' ) !== false || 
				strpos( $_SERVER['REQUEST_URI'], 'wordtwit_account_configuration' ) !== false || 
				( strpos( $_SERVER['REQUEST_URI'], 'post.php' ) !== false && isset( $_GET['wordtwit_post_action'] ) ) || 
				( isset( $_GET['page'] ) && $_GET['page'] == 'tweet_queue' )
			) 
		);
	}

	function is_post_page() {
		global $post;
		
		$allowable_post_types = array_merge( array( 'post' ), wordtwit_get_custom_post_types() );
		if ( !in_array( $post->post_type, $allowable_post_types ) ) {
			return false;	
		}
		
		return ( 
			is_admin() && 
			( strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) !== false || 
			  strpos( $_SERVER['REQUEST_URI'], 'post.php' ) !== false ) 
		);
	}

	function wordtwit_admin_head() {
		$current_scheme = get_user_option( 'admin_color' );
		$version_string = md5( WORDTWIT_VERSION );
		
		if ( $this->is_admin_section() ) {
			
			echo "<link rel='stylesheet' type='text/css' href='" . WORDTWIT_URL . "/admin/css/wordtwit-admin.css?ver=" . $version_string . "' />\n";

			echo "<link rel='stylesheet' type='text/css' href='" . WORDTWIT_URL . "/admin/css/wordtwit-admin-" . $current_scheme . ".css?ver=" . $version_string . "' />\n";
				
			if ( eregi( "MSIE", getenv( "HTTP_USER_AGENT" ) ) || eregi( "Internet Explorer", getenv( "HTTP_USER_AGENT" ) ) ) {
				echo "<link rel='stylesheet' type='text/css' href='" . WORDTWIT_URL . "/admin/css/wordtwit-admin-ie.css?ver=" . $version_string . "' />\n";
			}
		} else if ( $this->is_post_page() ) {
			echo "<link rel='stylesheet' type='text/css' href='" . WORDTWIT_URL . "/admin/css/wordtwit-post-widget.css?ver=" . $version_string . "' />\n";			
		}
	}
	
	function wordtwit_admin_js() {
		// admin_enqueue_scripts
		$version_string = md5( WORDTWIT_VERSION );		
		if ( $this->is_admin_section() ) {
			wp_enqueue_script( 'wordtwit-main', WORDTWIT_URL . '/admin/js/wordtwit-admin.js', 'jquery', $version_string );
			wp_enqueue_script( 'wordtwit-plugins', WORDTWIT_URL . '/admin/js/wordtwit-plugins-min.js', 'wordtwit-main', $version_string );			
		} else if ( $this->is_post_page() ) {
			wp_enqueue_script( 'wordtwit-main', WORDTWIT_URL . '/admin/js/wordtwit-post-widget.js', 'jquery', $version_string );
		}
		
		if ( $this->is_admin_section() || $this->is_post_page() ) {	
			$js_params = array(
				'admin_nonce' => wp_create_nonce( 'wordtwit_admin' ),
				'manual' => __( 'Manual', 'wordtwit-pro' ),
				'automatic' => __( 'Automatic', 'wordtwit-pro' ),
				'tweet_too_long' => __( 'Your tweet is too long. It must be 140 characters or less.', 'wordtwit-pro' ),
				'disabled' => __( 'Disabled for this post', 'wordtwit-pro' ),
				'unpublished' => __( 'Unpublished', 'wordtwit-pro' ),
				'reset_admin_settings' => __( 'Reset all WordTwit Pro admin settings?', 'wordtwit-pro' ) . ' ' . __( 'This operation cannot be undone.', 'wordtwit-pro' ),
				'custom_key_warning' => __( 'Warning: Changing your consumer key or secret will require you to reauthorize all of your accounts.', 'wordtwit-pro' )
			);
			
			if ( isset( $_GET['post'] ) ) {
				$js_params['post'] = $_GET['post'];	
			} 
			
			wp_localize_script( 
				'wordtwit-main', 
				'WordTwitProCustom', 
				$js_params
			);				
		}		
	}
	
	function wordtwit_admin_init() {
		$is_wordtwit_page = ( strpos( $_SERVER['REQUEST_URI'], 'wordtwit-pro' ) !== false );
		$is_plugins_page = ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false );
							
		// W.C.e need the BNCAPI for checking for plugin updates and all the wordtwit-pro admin functions
		if ( $is_wordtwit_page || $is_plugins_page ) {
			$this->setup_bnc_api();
			$this->check_for_update();
		}
	}
	
	function wordtwit_admin_footer() {
		global $post;
		
		if ( $this->is_admin_section() || $this->is_post_page() ) {
			echo "<script type='text/javascript'>\n";			
			if ( $post && isset( $post->ID ) && $post->post_type != 'tweet' ) {
				echo "var WordTwitPostID = '" . $post->ID . "';\n";
				echo "var WordTwitTweetStatus = " . wordtwit_get_tweet_status() . ";\n";
				echo "var WordTwitLoadJS = '1';\n";
			} else {
				echo "var WordTwitLoadJS = '0';\n";
			}
			echo "</script>\n";				
		}
	}
	
	function get_settings() {
		// check to see if we've already loaded the settings
		if ( $this->settings ) {
			return apply_filters( 'wordtwit_settings', $this->settings );	
		}
		
		//update_option( WORDTWIT_SETTING_NAME, false );
		$this->settings = get_option( WORDTWIT_SETTING_NAME, false );
		if ( !is_object( $this->settings ) ) {
			$this->settings = unserialize( $this->settings );	
		}

		if ( !$this->settings ) {
			// Return default settings
			$this->settings = new WordTwitSettings;
			$defaults = apply_filters( 'wordtwit_default_settings', new WordTwitDefaultSettings );

			foreach( (array)$defaults as $name => $value ) {
				$this->settings->$name = $value;	
			}

			return apply_filters( 'wordtwit_settings', $this->settings );	
		} else {	
			// first time pulling them from the database, so update new settings with defaults
			$defaults = apply_filters( 'wordtwit_default_settings', new WordTwitDefaultSettings );
			
			// Merge settings with defaults
			foreach( (array)$defaults as $name => $value ) {
				if ( !isset( $this->settings->$name ) ) {
					$this->settings->$name = $value;	
				}
			}

			return apply_filters( 'wordtwit_settings', $this->settings );	
		}			
	}
	
	function save_settings( $settings ) {
		$settings = apply_filters( 'wordtwit_update_settings', $settings );

		$serialized_data = serialize( $settings );
				
		update_option( WORDTWIT_SETTING_NAME, $serialized_data );	
		
		$this->settings = $settings;
	}	
	
	function setup_bnc_api() {
		if ( !$this->bnc_api ) {
			require_once( WORDTWIT_DIR . '/include/classes/bnc-api.php' );
			
			$settings = $this->get_settings();
					
			$this->bnc_api = new WordTwitBNCAPI( $settings->bncid, $settings->license_key );	
		}
	}	
	
	function process_submitted_settings() {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			do_action( 'wordtwit_settings_loaded' );
			return;	
		}
		
		if ( isset( $this->post['wordtwit-submit'] ) ) {
			$this->verify_post_nonce();
			
			$settings = $this->get_settings();
			$invalidate_bnc_api = false;
			
			$old_consumer_key = $settings->custom_consumer_key;
			$old_consumer_secret = $settings->custom_consumer_secret;
			
			// The license key information has changed
			if ( $settings->bncid != $this->post['bncid'] || $settings->wordtwit_license_key != $this->post['wordtwit_license_key'] ) {						
				$invalidate_bnc_api = true;
			
				$settings->last_bncid_result = false;
				$settings->last_bncid_licenses = false;
				$settings->bncid_had_license = false;								
				$settings->last_bncid_time = 0;				
			}
			
			foreach( (array)$settings as $name => $value ) {
				if ( isset( $this->post[ $name ] ) ) {
					
					// Remove slashes if they exist
					if ( is_string( $this->post[ $name ] ) ) {						
						$this->post[ $name ] = htmlspecialchars_decode( $this->post[ $name ] );
					}	
					
					$settings->$name = apply_filters( 'wordtwit_setting_filter_' . $name, $this->post[ $name ] );	
				} else {
					// Remove checkboxes if they don't exist as data
					if ( isset( $this->post[ $name . '-hidden' ] ) ) {
						$settings->$name = false;
					}
					
					// check to see if the hidden fields exist
					if ( isset( $this->post[ $name . '_1' ] ) ) {
						// this is an array field
						$setting_array = array();
						
						$count = 1;							
						while ( true ) {
							if ( !isset( $this->post[ $name . '_' . $count ] ) ) {
								break;	
							}	
							
							// don't add empty strings
							if ( $this->post[ $name . '_' . $count ] ) {
								$setting_array[] = $this->post[ $name . '_' . $count ];
							}
							
							$count++;
						}
						
						$settings->$name = $setting_array;	
					}
				}
			}
			
	
			if ( ( $old_consumer_key != $settings->custom_consumer_key ) || ( $old_consumer_secret != $settings->custom_consumer_secret ) ) {
				$settings->accounts = array();
			}	
		
			$this->save_settings( $settings );
			
			if ( $invalidate_bnc_api ) {
			
				// Clear the BNCID cache whenever we save information
				// will force a proper API call next load

				$this->bnc_api = false;
				
				$this->setup_bnc_api();
				$this->bnc_api->invalidate_all_tokens();	
			}			
			
			do_action( 'wordtwit_settings_saved' );
			
		} else if ( isset( $this->post['wordtwit-submit-reset'] ) ) {
			$this->verify_post_nonce();
			
			// rove the setting from the DB
			update_option( WORDTWIT_SETTING_NAME, false );
			$this->settings = false;
		} 	
		
		do_action( 'wordtwit_settings_loaded' );	
	}	
	
	function verify_post_nonce() {	 
		$nonce = $this->post['wordtwit-admin-nonce'];
		if ( !wp_verify_nonce( $nonce, 'wordtwit-post-nonce' ) ) {
			die( __( 'Unable to verify WordTwit Pro nonce', 'wordtwit-pro' ) );	
		}		
		
		return true;
	}	
	
	function verify_get_nonce() {
		$nonce = $this->get['wordtwit_nonce'];
		if ( !wp_verify_nonce( $nonce, 'wordtwit' ) ) {
			die( __( 'Unable to verify WordTwit Pro nonce', 'wordtwit-pro' ) );	
		}		
		
		return true;			
	}
	
	function get_twitter_auth_url() {
		$token = $this->oauth->get_request_token();
		if ( $token ) {
			$settings = $this->get_settings();
			
			$settings->oauth_request_token = $token['oauth_token'];
			$settings->oauth_request_token_secret = $token['oauth_token_secret'];
			
			$this->save_settings( $settings );
			
			return $this->oauth->get_auth_url( $token['oauth_token'] );
		} else {
			return false;	
		}
	}		
	
	
	function has_site_license() {
		$licenses = $this->bnc_api->user_list_licenses();	
		if ( $licenses ) {
			$this_site = $_SERVER['HTTP_HOST'];
			return ( in_array( $this_site, (array)$licenses['licenses'] ) );
		} else {
			return false;	
		}
	}		
}
