<?php
/*
Plugin Name: BlogGlue Related Posts Network Plugin
Plugin URI: http://www.blogglue.com
Description: BlogGlue Related Posts Network Plugin adds links at the bottom of every post to other similar posts on your blog and partner blogs you pick.  Get noticed in a community.
Author: BlogGlue
Author URI: http://www.blogglue.com
Version: 5.2
*/

/*  
Copyright (c) 2007 - 2011 Arkayne, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

global $arkayne_server;
$arkayne_server = 'www.blogglue.com';
//$arkayne_server = '127.0.0.1:8000';

global $arkayne_plugin;
$arkayne_plugin = plugin_basename(__FILE__);

global $arkayne_handler;
$arkayne_handler = str_replace("arkayne.php", "arkayne_admin.php", $arkayne_plugin);

if ( file_exists(ABSPATH.'bg-config.php') ) {
  require(ABSPATH.'bg-config.php');
}

require("arkayne_excerpt.php");
wp_enqueue_script('common');
wp_enqueue_script('wp-lists');
wp_enqueue_script('postbox');

function arkayne_token($post_id = -1)
{
  $token = '';

  if ($post_id != -1)
  {
    /* Get this category token */
    $cats = get_the_category($post_id);

    /* Get any category specific token */
    foreach ($cats as $cat)
    {
      $token = get_option('arkayne_token_'.$cat->term_id);
      if (!empty($token))
      {
        break;
      }
    }
  }

  /* Get global token */
  if (empty($token))
  {
    $token = get_option('arkayne_token');
  } 
  return trim($token);
}

function arkayne_token_public()
{
  list($temp_num, $temp_string) = split('-', arkayne_token(), 2);
  return $temp_num . '-' . md5($temp_string);
}

function arkayne_curl_url($arkayne_url)
{
  $ch = curl_init();
  $timeout = 3; // set to zero for no timeout

  if (get_option('arkayne_secure') == 'TRUE')
  {
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/arkayne_plugin.cert");
  }

  curl_setopt($ch, CURLOPT_URL, $arkayne_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $arkayne_data = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  return ($http_code == 200) ? $arkayne_data : '';
}

function arkayne_gfc_url($arkayne_url)
{
  if (get_option('arkayne_secure') == 'TRUE')
  {
    $context = stream_context_create(
    array(
      'ssl'=>array(
        'cafile'=>dirname(__FILE__) . "/arkayne_plugin.cert",
        'verify_peer'=>true
        )
      )
    );
    $arkayne_data = file_get_contents($arkayne_url, false, $context);
  }
  else
  {
    $arkayne_data = file_get_contents($arkayne_url);
  }

  return ($arkayne_data == false) ? '' : $arkayne_data;
}

function arkayne_fetch_url($arkayne_url)
{
  if (function_exists('curl_init'))
  {
    return arkayne_curl_url($arkayne_url);
  }
  else if (ini_get('allow_url_fopen'))
  {
    return arkayne_gfc_url($arkayne_url);
  }
  return '<!-- BlogGlue Error: Need CURL or fOpen. -->';
}

function arkayne_link($post_id, $cache_type) 
{
  global $wpdb;
  global $post;
  global $arkayne_server;
  
  $arkayne_url = '';

  if (get_option('arkayne_secure') == 'TRUE') { $arkayne_url = "https://" . $arkayne_server . "/secure/plugin/" . arkayne_token($post->ID) . "/wordpress/"; }
  else { $arkayne_url = "http://" . $arkayne_server . "/plugin/" . arkayne_token($post->ID) . "/wordpress/"; }

  if ($cache_type == 'rss') { $arkayne_url .= "rss/"; }
  $arkayne_url .= "?url=" . urlencode(get_permalink($post->ID));

  return arkayne_fetch_url($arkayne_url);
}

function get_blogglue_content($cache_type = 'post')
{
  global $post;
  global $wpdb;
  $cache_indicator = '<!-- BlogGlue Cache: Yes -->';
  $buffer = '';

  $when = get_post_meta($post->ID, 'arkayne-time-' . $cache_type, true);
  if (empty($when) or time() - $when > get_option('arkayne_cache_seconds'))
  {
    $cache_indicator = '<!-- BlogGlue Cache: No -->';
    $buffer = arkayne_link($post->ID, $cache_type);

    if (!empty($buffer))
    {
      // update links only if they are valid
      update_post_meta($post->ID, 'arkayne-cache-' . $cache_type, $buffer);
   
      // set flag to clear linking message once any page comes in
      update_option('arkayne_active', 1);
    }
    // update cache time to prevent timeouts from stacking up if server is down
    update_post_meta($post->ID, 'arkayne-time-'. $cache_type, time());
  }
  else
  {
    $row = $wpdb->get_row("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='arkayne-cache-$cache_type' AND post_id=$post->ID LIMIT 1" );
    $buffer = $row->meta_value;
  }
  return $cache_indicator . $buffer;
}

function arkayne_the_content($content)
{
  global $ARKAYNE_IS_EXCERPT;

  if ( get_option('arkayne_custom_position') != 'TRUE') {
    $cache_type = 'post';
    if (is_feed())
    {
      $cache_type = 'rss';
    } 


    if ($ARKAYNE_IS_EXCERPT == 0 && !is_preview() && !is_attachment() && !is_null($content) && !is_admin() && (is_single() || get_option('arkayne_show_list') == 'TRUE') && (!is_page() || get_option('arkayne_show_page') == 'TRUE'))
    {
      $buffer = get_blogglue_content($cache_type);
      $content .= PHP_EOL.'<!--more-->'.$buffer;
    }
  }
  return $content;
}

add_filter('the_content', 'arkayne_the_content', 2);
add_filter('the_content_rss', 'arkayne_the_content', 1);

function arkayne_activated( $plugin, $network_wide ) {
	$deactivate_plugin = 'yet-another-related-posts-plugin/yarpp.php';

	if ( plugin_basename(__FILE__) != $plugin )
		return;

	if ( !is_plugin_active( $deactivate_plugin ) )
		return;

	deactivate_plugins( $deactivate_plugin );
	add_option( 'arkayne_show_yarpp_deactivated_message', true );
	update_option('recently_activated', array($deactivate_plugin => time()) + (array)get_option('recently_activated'));
}
add_action( 'activated_plugin', 'arkayne_activated', 10, 2 );

if ( get_option( 'arkayne_show_yarpp_deactivated_message', false ) ) {
	function arkayne_yarpp_warning() {
		echo "\n<div id='yarpp-warning' class='updated fade'><p><strong>YARPP has been deactivated.</strong> Your settings have been saved.</p></div>\n";
		delete_option( 'arkayne_show_yarpp_deactivated_message' );
	}
	add_action('all_admin_notices', 'arkayne_yarpp_warning');
}

include("arkayne_setup.php");
include("arkayne_admin.php");
include("arkayne_badge.php");
include("arkayne_seo.php");
?>
