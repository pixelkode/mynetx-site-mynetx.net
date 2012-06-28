<?php
/*  
Copyright (c) 2007 - 2010 Arkayne, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

function arkayne_notices()
{
  global $post;
  global $arkayne_plugin;

  if (!function_exists('curl_init') && !ini_get('allow_url_fopen'))
  {
    echo '<div class="error fade" style="background-color:yellow !important;"><p style="text-align:center;"><strong>Your PHP is missing functionality required by BlogGlue, please install <a href="http://php.net/manual/en/book.curl.php" target="_blank">CURL</a> or <a href="http://php.net/manual/en/filesystem.configuration.php" target="_blank">Allow URL fOpen</a>.</strong></p></div>';
  }
  else if (is_plugin_active($arkayne_plugin) && get_option('arkayne_token') == '')
  {
    echo '<div class="error fade" style="background-color:yellow !important;"><p style="text-align:center;"><strong>The BlogGlue Related Community Plugin must be configured. Please fill out the form below.</strong></p></div>';
  }
  else if (get_option('arkayne_active', 0) == 0)
  {
    $arkayne_posts = get_posts('numberposts=100');
    if ( count($arkayne_posts) > 0) 
    {
      foreach($arkayne_posts as $post) 
      {
        arkayne_link($post->ID, 'post');
      }
      echo '<div class="error fade" style="background-color:yellow !important;"><p style="text-align:center;"><strong>The BlogGlue plugin is active, and evaluating your posts, this message will disappear once your links are ready.<br/></div>';
    } 
    else
    {
      echo '<div class="error fade" style="background-color:yellow !important;"><p style="text-align:center;"><strong>The BlogGlue plugin is active, however you currently do not have any content to evaluate.<br/></div>';
    }
  }
}

function arkayne_wordpress_settings()
{
  global $arkayne_server;
?>
  <table cellpadding="10" cellspacing="5">
  <tr>
    <th width="150" align="right"><?php _e('Links On Index:', 'wpqc'); ?></th>
    <td><input name="arkayne_show_list" type="checkbox" value="TRUE" <?php if (get_option('arkayne_show_list') == 'TRUE') echo 'checked' ?>/><span>Optional</span></td>
    <td class="arkayne_plugin_help"><?php _e('Check this option to show related links on home and archive pages.  <strong>Full, not excerpt,  posts must be enabled for this to work.</strong>', 'wpqc'); ?></td>
  </tr>
  <tr>
    <th align="right"><?php _e('Links On Pages:', 'wpqc'); ?></th>
    <td><input name="arkayne_show_page" type="checkbox" value="TRUE" <?php if (get_option('arkayne_show_page') == 'TRUE') echo 'checked' ?>/><span>Optional</span></td>
    <td class="arkayne_plugin_help"><?php _e('Check this option to show the related links on pages (not posts) you created in WordPress.', 'wpqc'); ?></td>
  </tr>
  <tr>
    <th align="right"><?php _e('SEO Test Button:', 'wpqc'); ?></th>
    <td><input name="arkayne_show_seo" type="checkbox" value="TRUE" <?php if (get_option('arkayne_show_seo') == 'TRUE') echo 'checked' ?>/><span>Optional</span></td>
    <td class="arkayne_plugin_help"><?php _e('Check this option to show the SEO Test button on edit post and pages screens.', 'wpqc'); ?></td>
  </tr>
  <tr>  <th align="right"><?php _e('Extra Security:', 'wpqc'); ?></th>
    <td><input name="arkayne_secure" type="checkbox" value="TRUE" <?php if (get_option('arkayne_secure') == 'TRUE') echo 'checked' ?>/><span>Optional</span></td>
    <td class="arkayne_plugin_help"><?php _e('BlogGlue does NOT transmit anything requiring security so this option is for paranoid people.  If your server does not support secure connections BlogGlue may not work properly. This is not required for normal operation, for most installations regular security is enough.  Enable this option only if your server supports secure connections.', 'wpqc'); ?></td>
  </tr>
  <tr>
  <th align="right"><?php _e('Cache Seconds:', 'wpqc'); ?></th>
    <td><input name="arkayne_cache_seconds" type="text" value="<?php echo get_option('arkayne_cache_seconds', '3600') ?>"/><span>Optional</span></td>
    <td class="arkayne_plugin_help"><?php _e('For improved load performance set this to ( <strong>3600</strong> ) seconds.  Leave blank, this is not recommended, to never cache.  Links will update no sooner than the cache value after you make changes to your partners, titles, or account prefernces.', 'wpqc'); ?></td>
  </tr>
  </table>
<?php
}

function arkayne_advanced_settings() 
{
  global $arkayne_server;
?>
  <table cellpadding="10" cellspacing="5">
  <tr>
    <th width="150" align="right"><?php _e('Account Token:', 'wpqc'); ?></th>
    <td><input name="arkayne_token" type="text" value="<?php echo get_option('arkayne_token') ?>" size="40"/><span>Required</span></td>
    <td class="arkayne_plugin_help"><?php _e('This token was put here when you installed BlogGlue. If you remove it you will need to attach another BlogGlue account. <strong>This works for all categories as well by default.</strong>', 'wpqc') ?></td>
  </tr>
  <tr>
    <th width="150" align="right"><?php _e('Custom Placement:', 'wpqc'); ?></th>
    <td><input name="arkayne_custom_position" type="checkbox" value="TRUE" <?php if (get_option('arkayne_custom_position') == 'TRUE') echo 'checked' ?>/><span>Optional</span></td>
    <td class="arkayne_plugin_help"><?php _e('Check this option to manually insert BlogGlue into your template.  <strong>This option is not recommended.</strong>  You must enter <i>&lt;?php echo get_blogglue_content(); ?&gt;</i> in your template.', 'wpqc'); ?></td>
  <?php $categories = get_categories('child_of=0'); foreach ($categories as $cat) { ?>
    <tr>
      <th align='right'><?php _e('Category ' . $cat->cat_name . ' Token:', 'wpqc') ?></th>
      <td align="left"><input name="<?php echo 'arkayne_token_'.$cat->term_id ?>" type="text" value="<?php echo get_option('arkayne_token_'.$cat->term_id, '') ?>"/><span>Optional</span></td>
      <td class="arkayne_plugin_help"><?php _e('In some instances advanced users can set up multiple BlogGlue accounts within the same blog.  If you need to do this please <a href="http://www.blogglue.com/contact/" target="_blank">contact BlogGlue directly</a>.', 'wpqc'); ?></td>
    </tr>
  <?php } ?>
  </table>
<?php
}

function arkayne_settings() {
  add_meta_box('arkayne_wordpress_settings', 'WordPress Settings', 'arkayne_wordpress_settings', 'BlogGlue Settings');
  add_meta_box('arkayne_advanced_settings', 'Advanced Settings', 'arkayne_advanced_settings', 'BlogGlue Settings');
}

function arkayne_admin_form_config()
{
  global $arkayne_handler;
?>
<style type="text/css"> 
#arkayne_plugin_admin {
  padding-right: 20px;
}

.arkayne_token_section {
  font-size: 1.8em;
  height: 56px;
}

.arkayne_token_section input {
  margin-top: 5px;
  font-size: 1.0em;
  border: solid 1px #AAA;
  width: 400px;
}

#arkayne_plugin_admin table {
  margin-top: 20px;
}

#arkayne_plugin_admin table td span { 
  display: block;  
  font-size: 10px;
  color: #666;
}

#arkayne_plugin_admin table th {
  border: 0px;
  font-weight: bold;
  vertical-align: top;
  padding-right: 20px;
}

#arkayne_plugin_admin table td {
  border: 0px;
  height: 80px;  vertical-align: top;
}

td.arkayne_plugin_help {
  font-size: 11px;
  padding: 0px 20px 0px 20px;
}

#arkayne_plugin_categories table td {
  height: 30px;
  padding-right: 10px;
}

#arkayne_additional_settings div {
  font-size: 1.0em;
  line-height: 1.4em;
}

#arkayne_additional_settings h3 {
  font-size: 12px;
  font-weight: bold;
  line-height: 1;
  margin: 0px;
  padding: 7px 9px;
}

</style>

<!--
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		// postboxes setup
                $('.postbox').addClass('closed');
		postboxes.add_postbox_toggles('arkayne');
	});
	//]]>
</script>
-->

<div class="wrap">
 <div id="arkayne_plugin_admin">
  <h2><?php _e('BlogGlue Settings', 'wpqc'); ?></h2>

  <iframe src="<?php echo arkayne_dashboard_url(); ?>" width="100%", height="500px" style="margin: 0px;">
    <p>Your browser does not support iframes.</p>
  </iframe>
  <p>&nbsp;</p>
  <form name="form1" method="post" action="<?php echo get_option('siteurl') . '/wp-admin/admin.php?page=' . $arkayne_handler; ?>">
  <input type="hidden" name="arkayne_stage" value="arkayne_process"/>
  <div id="arkayne_additional_settings">
  <?php do_meta_boxes('BlogGlue Settings', 'advanced', null) ?>
  </div>
  <p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Plugin Settings', 'wpqc'); ?> &raquo;" /></p>
  </form>
  <p style="font-size: 0.8em; text-align: center;"><a href="http://www.blogglue.com/communities/" target="_blank">Click Here</a> to visit your online BlogGlue Account and manage your community partenrs.</p>
 </div>
</div>
<?php
}

function arkayne_admin_form_setup()
{
  global $arkayne_handler;
?>
  <div class="wrap">
    <div style="width: 1000px; margin: 10px 0px; padding: 0px 20px 40px 20px; background-color: #efefef; border: 1px solid #EBEBEB;">
      <h2>Setup BlogGlue</h2>
      <iframe src="<?php echo arkayne_register_url('Configuration', TRUE); ?>" width="100%", height="700px" style="margin: 0px;">
        <h3>Sorry... Your browser does not support frames.</h3>        <p>BlogGlue was designed to easily install using an iframe OAuth setup.  Try this in a different browser or...</p>        <p>To install manually, please visit <a href="<?php echo arkayne_register_url('IFrame', FALSE); ?>" target="_blank">BlogGlue Registration ( opens in new window )</a> to get your private account token.</p>
      </iframe>
        <p>Enter the token from the <a href="http://www.blogglue.com/help/install_instructions/">installation page</a> after signup in the field below...</p>
        <form method="post" action="<?php echo get_option('siteurl') . '/wp-admin/admin.php?page=' . $arkayne_handler; ?>">
          <input type="hidden" name="arkayne_stage" value="arkayne_process"/>
          <strong>Paste Token Here: </strong><input name="arkayne_token" type="text" value="<?php echo get_option('arkayne_token') ?>" size="40"/>
          <input type="submit" value="<?php _e('Apply Token', 'wpqc'); ?> &raquo;" />
        </form>
      </iframe>
    </div>
  </div>
<?php 
} 

function arkayne_admin_form()
{
  arkayne_notices();

  if (get_option('arkayne_token') == '' ) 
  { 
    arkayne_admin_form_setup(); 
  }
  else 
  { 
    arkayne_admin_form_config(); 
  }
}

function arkayne_admin()
{
  add_options_page('BlogGlue Settings', 'BlogGlue', 9, __FILE__, 'arkayne_admin_form');
}

function arkayne_settings_link($links) 
{
  global $arkayne_handler;

  $settings_link = '<a href="options-general.php?page=' . $arkayne_handler . '">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

// Secure the process of updating the plugin
if (is_admin())
{
  add_filter("plugin_action_links_" . $arkayne_plugin, 'arkayne_settings_link' );

  // Catch save event on the admin page
  if ('arkayne_process' == $_POST['arkayne_stage'])
  {
    update_option('arkayne_token', $_POST['arkayne_token']);
    update_option('arkayne_secure', $_POST['arkayne_secure']);
    update_option('arkayne_show_list', $_POST['arkayne_show_list']);
    update_option('arkayne_show_page', $_POST['arkayne_show_page']);
    update_option('arkayne_show_seo', $_POST['arkayne_show_seo']);
    update_option('arkayne_cache_seconds', $_POST['arkayne_cache_seconds']);
    update_option('arkayne_custom_position', $_POST['arkayne_custom_position']);

    $categories = get_categories('child_of=0');
    foreach ($categories as $cat)
    {
      update_option('arkayne_token_'.$cat->term_id, $_POST['arkayne_token_'.$cat->term_id]);
    }
  }

  // Do actions for admin
  // add_action('admin_notices', 'arkayne_notices') // Notices Show only on admin page (called from there)
  add_action('admin_menu', 'arkayne_admin');
  add_action('admin_menu', 'arkayne_settings');
}
?>
