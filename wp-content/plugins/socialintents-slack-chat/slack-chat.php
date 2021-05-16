<?php
/*
Plugin Name: Slack Live Chat
Plugin URI: http://www.socialintents.com
Description: Slack Live Chat lets you chat with website visitors right from Slack. To get started: 1) Click the "Activate" link to the left of this description, 2) Go to your Slack Chat plugin Settings page, and click Get My API Key.
Version: 1.2.46
Author: Social Intents
Author URI: http://www.socialintents.com/
*/

$scsi_domain = plugins_url();
add_action('init', 'scsi_init');
add_action('admin_notices', 'scsi_notice');
add_filter('plugin_action_links', 'scsi_plugin_actions', 10, 2);
add_action('wp_footer', 'scsi_insert',4);
add_action('admin_footer', 'scsiRedirect');
define('scsi_DASHBOARD_URL', "https://www.socialintents.com/dashboard.do?wp=true");
define('scsi_SMALL_LOGO',plugin_dir_url( __FILE__ ).'ac-small-white.png');
function scsi_init() {
    if(function_exists('current_user_can') && current_user_can('manage_options')) {
        add_action('admin_menu', 'scsi_add_settings_page');
        add_action('admin_menu', 'scsi_create_menu');
    }
}
function scsi_insert() {

    global $current_user;
    if(strlen(get_option('scsi_widgetID')) == 32 ) {
	echo("\n<!-- Slack Chat by Social Intents (www.socialintents.com) -->\n<script src='//www.socialintents.com/api/socialintents.1.3.js#".get_option('scsi_widgetID')."' async='async'></script>\n");
    }
}

function scsi_notice() {
    if(!get_option('scsi_widgetID')) echo('<div class="error" style="padding:10px;"><p><strong><a style="text-decoration:none;border-radius:3px;color:white;padding:10px; ;background:#029dd6;border-color:#06b9fd #029dd6 #029dd6;margin-right:20px;"'.sprintf(__('href="%s">Activate your Slack Chat account</a></strong>  Almost done - Activate your account and start chatting!' ), admin_url('options-general.php?page=slack-chat')).'</p></div>');
}

function scsi_plugin_actions($links, $file) {
    static $this_plugin;
    $scsi_domain = plugins_url();
    if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
    if($file == $this_plugin && function_exists('admin_url')) {
        $settings_link = '<a href="'.admin_url('options-general.php?page=slack-chat').'">'.__('Settings', $scsi_domain).'</a>';
        array_unshift($links, $settings_link);
    }
    return($links);
}

function scsi_add_settings_page() {
    function scsi_settings_page() {
        global $scsi_domain ?>
	<div class="wrap">
        <p style="margin-left:4px;font-size:16px;"><strong>Slack Chat</strong> by Social Intents: <?php wp_nonce_field('update-options') ?>
		<a href="http://www.socialintents.com/slack-live-chat.jsp" target="_blank" title="Slack Chat">Sell more and keep customers happy right from Slack.</p>
 		
	<div id="scsi_register" class="inside" style="padding: -30px 10px"></p>	
    	<div class="postbox" style="max-width:600px;height:50px;padding:30px;">
            	
		<div style="float:left">	
			<b>Activate Slack Chat</b> <br>
			<p>Login or sign up now for free.</p>
		</div>
		<div><a href='https://www.socialintents.com/slack-live-chat.jsp?source=wp' class="right button button-primary" target="_blank">Add to Slack and Get My API Key</a></div>
		</div>

   		<div class="postbox" style="max-width:600px;height:50px;padding:30px;">
            	<div style="float:left">
			<b>Enter Your API Key</b> <br>
			<p>If you already know your API Key.</p>
		</div>
		<div class="">
		<form id="saveSettings" method="post" action="options.php">
                   <?php wp_nonce_field('update-options') ?>
			<div style="float:right">
			<input type="text" name="scsi_widgetID" id="scsi_widgetID" placeholder="Your API Key" value="<?php echo(get_option('scsi_widgetID')) ?>" style="margin-right:10px;" />
            <input type="hidden" name="page_options" value="scsi_widgetID" />
			<input type="hidden" name="action" value="update" />
            <input type="submit" class="right button button-primary" name="scsi_submit" id="scsi_submit" value="<?php _e('Save Key', $scsi_domain) ?>" /> 
			</div>
                </form>
		</div>
               
            	
	</div>
	</div>
	

	<div id="scsi_registerComplete" class="inside" style="padding: -20px 10px;display:none;">
	<div class="postbox" style="max-width:600px;height:250px;padding:30px;padding-top:5px">
<h3 class=""><span id="sicp_noAccountSpan">Slack Chat Settings</span></h3>
		<p>Customize your live chat by selecting 'Customize' below. </p>
		<p>
		<div style="text-align:center">
		
		<a href='https://www.socialintents.com/dashboard.do?wp=true' class="button button-primary" target="_ac">Dashboard</a>&nbsp;
<a href='https://www.socialintents.com/apps.do' class="button button-primary" target="_ac">Customize</a>&nbsp;
		
<a href='https://www.socialintents.com/preview.do?acct=<?php echo(get_option('scsi_widgetID')) ?>' class="button button-primary" target="_ac">Popup Preview</a>&nbsp;
		<br><br><a id="changeWidget" class="" target="_blank">Enter Different API Key</a>&nbsp;
		</div>
		</p>

</div>
</div>
<script>
jQuery(document).ready(function($) {

var scsi_wid= $('#scsi_widgetID').val();
if (scsi_wid=='') 
{}
else
{

	$( "#scsi_enterwidget" ).hide();
	$( "#scsi_register" ).hide();
	$( "#scsi_registerComplete" ).show();
	$( "#scsi_noAccountSpan" ).html("Slack Chat Plugin Settings");

}

$(document).on("click", "#scsi_inputSaveSettings", function () {
	$( "#saveDetailSettings" ).submit();
});

$(document).on("click", "#changeWidget", function () {
$( "#scsi_register" ).show();
$( "#scsi_inputSaveSettings" ).hide();
});


});
</script>
<?php }
$scsi_domain = plugins_url();
add_submenu_page('options-general.php', __('Slack Chat', $scsi_domain), __('Slack Chat', $scsi_domain), 'manage_options', 'slack-chat', 'scsi_settings_page');
}
function addscsiLink() {
$dir = plugin_dir_path(__FILE__);
include $dir . 'options.php';
}
function scsi_create_menu() {
  $optionPage = add_menu_page('Slack Chat', 'Slack Chat', 'administrator', 'scsi_dashboard', 'addscsiLink', plugins_url('si-small.png', __FILE__ ));
}
function scsiRedirect() {
$redirectUrl = "https://www.socialintents.com/dashboard.do?wp=true";
echo "<script> jQuery('a[href=\"admin.php?page=scsi_dashboard\"]').attr('href', '".$redirectUrl."').attr('target', '_blank') </script>";}
?>