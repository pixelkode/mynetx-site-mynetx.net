<?php

function wordtwit_get_saved_tweet_info( $post_id = false ) {
	global $post;
	
	if ( $post_id ) {
		$post = get_post( $post_id );	
	} else {
		$post_id = $post->ID;	
	}
	
	$tweet_info = get_post_meta( $post->ID, 'wordtwit_post_info', true );
	
	//print_r( $tweet_info );
	if ( !$tweet_info ) {
		$tweet_info = new stdClass;
		$tweet_info->manual = false;	
		$tweet_info->tweet_times = 1;
		$tweet_info->delay = 0;
		$tweet_info->enabled = 1;
		$tweet_info->separation = 60;
		
		if ( $post->post_status == 'publish' ) {
			$tweet_info->status = WORDTWIT_TWEET_IS_OLD;
		} else {
			$tweet_info->status = WORDTWIT_TWEET_UNPUBLISHED;
		}
		
		$tweet_info->result = array();
		$tweet_info->tweet_counter = 1;
					
		$tweet_info->tweet_log_ids = array();
		$tweet_info->hash_tags = array();	
		
		$tweet_info->accounts = array();
		$accounts = wordtwit_get_accounts();
		if ( $accounts && count( $accounts ) ) {
			foreach( $accounts as $name => $account ) {
				if ( $account->is_global ) {
					$tweet_info->accounts[] = $account->screen_name;
				}
			}		
		}
	} else {
		// Has tweet info
	}
	
	return apply_filters( 'wordtwit_saved_tweet_data', $tweet_info );
}

function wordtwit_get_pending_tweet_count() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info && $tweet_info->status == WORDTWIT_TWEET_PUBLISHED ) {
		global $wpdb;
		
		$sql = $wpdb->prepare( "SELECT post_status,count(*) as c FROM " . $wpdb->posts . " WHERE post_type = 'tweet' AND post_status = 'future' AND ID IN (" . implode( ",", $tweet_info->tweet_log_ids ) . ") GROUP by post_status" ); 
		$pending = $wpdb->get_row( $sql );
		if ( $pending ) {
			return $pending->c;
		}
	}
	
	return 0;
}

function wordtwit_get_publish_tweet_count() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info && $tweet_info->status == WORDTWIT_TWEET_PUBLISHED ) {
		global $wpdb;
		
		$sql = $wpdb->prepare( "SELECT post_status,count(*) as c FROM " . $wpdb->posts . " WHERE post_type = 'tweet' AND post_status = 'publish' AND ID IN (" . implode( ",", $tweet_info->tweet_log_ids ) . ") GROUP by post_status" ); 
		$pending = $wpdb->get_row( $sql );
		if ( $pending ) {
			return $pending->c;
		}
	}
	
	return 0;	
}

function wordtwit_save_tweet_info( $tweet_info, $post_id ) {
	delete_post_meta( $post_id, 'wordtwit_post_info' );
	add_post_meta( $post_id, 'wordtwit_post_info', $tweet_info );
}

function wordtwit_post_is_enabled() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		return $tweet_info->enabled;
	}
	
	return 0;	
}

function wordtwit_post_get_tweet_times() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		return $tweet_info->tweet_times;
	}
	
	return 1;		
}

function wordtwit_post_get_tweet_delay() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		return $tweet_info->delay;
	}
	
	return 0;		
}

function wordtwit_post_get_tweet_seperated_hours() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info && isset( $tweet_info->separation ) ) {
		return floor( $tweet_info->separation / 60 );
	}
	
	return 0;		
}

function wordtwit_post_get_tweet_seperated_mins() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info && isset( $tweet_info->separation ) ) {
		return ( $tweet_info->separation % 60 );
	}
	
	return 0;		
}

function wordtwit_post_is_account_enabled( $screen_name ) {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		return (  in_array( $screen_name, $tweet_info->accounts ) );
	}
	
	return false;	
}

function wordtwit_get_post_hash_tags( $post_id = false ) {
	global $post;
	
	if ( $post_id ) {
		$post = get_post( $post_id );	
	}

	$tweet_info = wordtwit_get_saved_tweet_info( $post_id );	
	return $tweet_info->hash_tags;
}

function wordtwit_post_has_hash_tags( $post_id = false ) {
	return count( wordtwit_get_post_hash_tags( $post_id ) );
}

function wordtwit_get_tweet_mode( $post_id = false ) {
	global $post;
	
	if ( $post_id ) {
		$post = get_post( $post_id );	
	}

	$tweet_info = wordtwit_get_saved_tweet_info( $post_id );
		
	return apply_filters( 'wordtwit_tweet_mode', ( $tweet_info->manual ? WORDTWIT_TWEET_MANUAL : WORDTWIT_TWEET_AUTOMATIC ) );	
}

function wordtwit_get_post_tweet( $post_id = false, $tweet_num = 1 ) {
	global $post;
	
	if ( $post_id ) {
		$post = get_post( $post_id );	
	}

	$tweet_info = wordtwit_get_saved_tweet_info( $post_id );	
	if ( !$tweet_info->manual ) {	
		$tweet_templates = wordtwit_get_tweet_templates();
		$settings = wordtwit_get_settings();
		
		if ( $settings->tweet_template == 'custom' ) {
			$template = $settings->custom_tweet_template;
		} else {
			$template = $tweet_templates[ $settings->tweet_template ];
		}
		
		$tweet = $template;
		
		if ( count( $tweet_info->hash_tags ) ) {	
			$tags = '#' . implode( $tweet_info->hash_tags, ' #' );
			$tweet = str_replace( '[hashtags]', $tags, $tweet );
		} else {
			$tweet = str_replace( '[hashtags]', '', $tweet );
		}
		
		$tweet = str_replace( '[link]', wordtwit_get_short_post_link( $tweet_num ), $tweet );
		$tweet = trim( $tweet );

		// Add author information
		if ( strpos( $tweet, 'author]' ) !== false ) {
			$post_author = get_userdata( $post->post_author );	
			
			if ( $post_author->display_name ) { 
				$tweet = str_replace( '[full_author]', $post_author->display_name, $tweet );
			} else if ( $post_author->first_name && $post_author->last_name ) {
				$tweet = str_replace( '[full_author]', $post_author->first_name . ' ' . $post_author->last_name, $tweet );
			}
		
			if ( $post_author->first_name && $post_author->last_name ) {
				$first_initial = $post_author->first_name[0];
				$short_name = $first_initial . '. ' . $post_author->last_name;	
				
				$tweet = str_replace( '[short_author]', $short_name, $tweet );
			} else if ( $post->author->nickname ) {
				$tweet = str_replace( '[short_author]', $post_author->nickname, $tweet );
			}
		}
		
		// Custom post type templates
		if ( strpos( $tweet, '[post_type]' ) !== false ) {
			$post_types = get_post_types( '', 'objects' );	
			if ( isset( $post_types[ $post->post_type ] ) ) {
				$this_post_type_info = $post_types[ $post->post_type ];
				$tweet = str_replace( '[post_type]', $this_post_type_info->labels->singular_name, $tweet );	
			}
		}
	
		// Do title shortening if it is enabled via the settings
		$test_tweet = str_replace( '[title]', get_the_title(), $tweet );
		if ( strlen( $test_tweet ) > 140 && $settings->shorten_title ) {
			$reduce_title_by = strlen( $test_tweet ) - 140 + 2;
			
			if ( strlen( get_the_title() ) > $reduce_title_by ) {
				$new_title = substr( get_the_title(), 0, strlen( get_the_title() ) - $reduce_title_by ) . '… ';
				
				$tweet = str_replace( '[title]', $new_title, $tweet );
			} else {
				$tweet = $test_tweet;
			}	
		} else {
			$tweet = $test_tweet;	
		}
	} else {
		$tweet = $tweet_info->text;
	}
	
	return apply_filters( 'wordtwit_post_tweet', $tweet );	
}

function wordtwit_get_post_tweet_accounts() {
	$accounts = array();
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		$settings = wordtwit_get_settings();
		foreach( $tweet_info->accounts as $account ) {
			$account_info = new stdClass;
			$account_info->name = $account;
			$account_info->active = isset( $settings->accounts[ $account ] );
			
			$accounts[ $account_info->name ] = $account_info;	
		}
	}
	
	return $accounts;	
}

function wordtwit_has_post_active_accounts() {
	$accounts = wordtwit_get_post_tweet_accounts();
	$active_accounts = 0;
	
	foreach( $accounts as $name => $info ) {
		if ( $info->active ) {
			$active_accounts++;	
		}	
	}
	
	return $active_accounts;
}

function wordtwit_the_tweet_accounts() {
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		echo implode( ', ', $tweet_info->accounts );	
	}	
}

function wordtwit_get_tweet_status() {
	$tweet_status = WORDTWIT_TWEET_UNPUBLISHED;	
	$tweet_info = wordtwit_get_saved_tweet_info();
	if ( $tweet_info ) {
		$tweet_status = $tweet_info->status;
	}	
	
	return apply_filters( 'wordtwit_tweet_status', $tweet_status );	
}

function wordtwit_the_tweet_status() {
	echo wordtwit_get_tweet_status();	
}

function wordtwit_the_post_tweet() {
	echo wordtwit_get_post_tweet();	
}

function wordtwit_get_recent_hash_tags() {
	global $wpdb;
	$hash_tags = array();
	
	$sql = $wpdb->prepare( 'SELECT post_id,meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s ORDER by post_id DESC', 'wordtwit_post_info' );
	$tweet_entries = $wpdb->get_results( $sql );
	if ( $tweet_entries ) {
		foreach( $tweet_entries as $entry ) {
			$tweet_info = maybe_unserialize( $entry->meta_value );
			if ( $tweet_info ) {
				if ( isset( $tweet_info->hash_tags ) && count( $tweet_info->hash_tags ) ) {
					foreach( $tweet_info->hash_tags as $hash_tag ) {
						if ( !isset( $hash_tags[ $hash_tag ] ) ) {
							$tag = new stdClass;
							
							$tag->name = $hash_tag;
							$tag->count = 1;
							
							$hash_tags[ $hash_tag ] = $tag;
						} else {
							$hash_tags[ $hash_tag ]->count++;
						}	
					}	
				}	
			}
		}	
	}
	
	if ( count ( $hash_tags ) ) {
		$counted_tags = array();
		foreach( $hash_tags as $name => $tag ) {
			$counted_tags[ $tag->count ][] = $tag;
		}
		
		krsort( $counted_tags );
		
		$hash_tags = array();
		foreach( $counted_tags as $count => $tag ) {
			if ( is_array( $tag ) ) {
				foreach( $tag as $each_tag ) {
					$hash_tags[ $each_tag->name ] = $each_tag;		
				}	
			} else {
				$hash_tags[ $tag->name ] = $tag;	
			}
		}
	}
	
	return apply_filters( 'wordtwit_recent_hash_tags', $hash_tags );
}

function wordtwit_get_retweet_post_url() {
	return add_query_arg( array( 'wordtwit_nonce' => wp_create_nonce( 'wordtwit' ), 'wordtwit_post_action' => 'retweet' ) );
}


function wordtwit_the_retweet_post_url() {
	echo wordtwit_get_retweet_post_url();
}


function wordtwit_is_post_box_area_disabled() {
	return ( !wordtwit_post_is_enabled() || ( wordtwit_get_tweet_status() == WORDTWIT_TWEET_PUBLISHED ) );
}

function wordtwit_get_publish_now_post_url() {
	return add_query_arg( array( 'wordtwit_nonce' => wp_create_nonce( 'wordtwit' ), 'wordtwit_post_action' => 'publish_now' ) );		
}

function wordtwit_the_publish_now_post_url() {
	echo wordtwit_get_publish_now_post_url();
}

