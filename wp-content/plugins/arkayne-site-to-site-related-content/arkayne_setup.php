<?php
/*  
Copyright (c) 2007 - 2010 Arkayne, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

function arkayne_dashboard_url()
{
  global $arkayne_server;
  return 'http://' . $arkayne_server . '/dashboard/wordpress/' . arkayne_token_public() . '/?utm_source=WordPress&utm_medium=Configuration';
}

function arkayne_register_url($medium='Unknown', $round_trip = TRUE)
{
  global $arkayne_handler;
  global $arkayne_server;

  $interstatial = '';
  $redirect = '';
  $affiliate = '';
  $user_agent = $_SERVER['HTTP_USER_AGENT']; 

  if (preg_match('/Safari/i',$user_agent) && !preg_match('/Chrome/i',$user_agent)) 
  { 
    $interstatial = '&interstatial=1';
  } 

  if ($round_trip)
  {
    $redirect = '&redirect=' . urlencode(get_option('siteurl') . '/wp-admin/admin.php?page=' . $arkayne_handler);
  }

  if ( defined('BLOGGLUE_AFFILIATE') ) 
  {
     $affiliate = '&coupon='.BLOGGLUE_AFFILIATE;
  }

  return 'http://' . $arkayne_server . '/register/free/wordpress/?utm_source=WordPress&utm_medium=' . $medium . $redirect . $interstatial . $affiliate;

}

function arkayne_setup() 
{
  global $arkayne_plugin;
  global $arkayne_server;

  if (current_user_can('activate_plugins') && is_plugin_active($arkayne_plugin) && get_option('arkayne_token') == '' && get_option( 'blogglue_install_runonce', false) == false)
  {  
    update_option( 'blogglue_install_runonce', true );
    echo '<script>window.onload=function(){ tb_show("BlogGlue Setup", "' . arkayne_register_url("Activation", TRUE) . 'TB_iframe=true&width=1000&height=660", null); }</script>';
  }
  else if (current_user_can('activate_plugins') && is_plugin_active($arkayne_plugin) && get_option('arkayne_token') == '')
  {
    echo '<div class="error fade" style="background-color:yellow !important;"><p style="text-align:center;"><strong>The BlogGlue Related Community Plugin is active and has not been configured.</strong></p></div>';
  }
}

add_action('admin_print_scripts-plugins.php', 'arkayne_setup' );

function arkayne_install() 
{
  global $arkayne_plugin;

  if (current_user_can('activate_plugins') && is_plugin_active($arkayne_plugin) && get_option('arkayne_token') == '' && isset($_POST['arkayne_auth_token']))
  {
    update_option('arkayne_token', $_POST['arkayne_auth_token']);
    update_option('arkayne_show_seo', 'TRUE');
    update_option('arkayne_cache_seconds', '3600');
  }
}

add_action('admin_init', 'arkayne_install', 1);

?>
