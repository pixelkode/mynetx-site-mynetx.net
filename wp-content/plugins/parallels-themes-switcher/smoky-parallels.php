<?php
/*
 * Plugin Name:  Parallels Theme Switcher
 * Plugin URI:  http://www.xhtmlweaver.com
 * Description: This plugin allows you to modify/switch the current theme on the live site without interfering the current visitors.
 * Author: XhtmlWeaver.com
 * Author URI: http://www.xhtmlweaver.com
 * Version: 1.0
 */


/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once(dirname(__FILE__).'/func/function.php');
function WPXW_the_options() {
    define(PLUGIN_NAME, "'parallels-themes-switcher'") 
    
?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>Parallels Theme Switcher Options</h2>

	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<h3>Parallels Theme Switcher Settings</h3>
		<table class="form-table">
		<tr valign="top">
			<th scope="row">Set Cookie Expiration</th>
			<td>
				<input name="WPXW_Cookie-Time" type="text" value="<?php echo get_option("WPXW_Cookie-Time"); ?>"/>
				<label style="margin-left:3px;" class="description">( in days)</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Choose the JS and CSS files location</th>
			<td>
				<select style="width:120px;text-align:center" name="WPXW_file">
					<option value="head"<?php if(get_option("WPXW_file")=='head')echo 'selected="selected"'; ?>>Head Section</option>
					<option value="foot"<?php if(get_option("WPXW_file")=='foot')echo 'selected="selected"'; ?>>Foot Section</option>
					<option value="custom"<?php if(get_option("WPXW_file")=='custom')echo 'selected="selected"'; ?>>Custom(advanced)</option>
				</select>
				<label style="margin-left:20px;" class="description">Custom: You will have to manually import Parallels Theme Switcher's CSS/JS</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Switch only available to Admin Users?</th>
			<td>
				<input name="WPXW_only_admin" type="checkbox" value="checkbox" <?php if(get_option("WPXW_only_admin")) echo "checked='checked'"; ?>/>
				<label style="margin-left:3px;" class="description">Only Admin</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Whitelisted IP Addresses
                <br/>one line one IP or split IP with <code>,</code> or <code>&#160;</code> or <code>|</code></th>
			<td>
				<textarea name="WPXW_excluded_ip" cols="50" rows="8" id="WPXW_excluded_ip" style="width:500px;font-size:12px;" class="code"><?php echo get_option('WPXW_excluded_ip'); ?></textarea>
			</td>
		</tr>
		</table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="WPXW_Cookie-Time,WPXW_file,WPXW_only_admin,WPXW_excluded_ip" />

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="Save Change" />
		</p>
	</form>
	
	<form method="post" action="">
		<h3>Duplicate the Theme for Editing</h3>
			<?php  if(isset($_POST['copy-theme']) && $_POST['copy-theme'] !== '' && isset($_POST['new-theme-name']) && $_POST['new-theme-name'] !== ''){
					$oldtheme=$_POST['copy-theme'];
					$newtheme=$oldtheme.'-'.$_POST['new-theme-name'];
					WPXW_copy_new_theme($oldtheme, $newtheme);
			}	?>
		<table class="form-table">

		<tr valign="top">
			<th scope="row">All Themes</th>
			<td>
				<select id="choosetheme" style="width:160px;text-align:center" name="copy-theme">
					<option value="0"></option>
				<?php
					$themes=get_themes();$current=get_current_theme();
					print_r($themes);
					foreach($themes as $theme){
						$name=$theme['Name'];
						$temp=$theme['Template'];
						echo '<option value="'. $temp .'"';
						if($name == $current)echo 'selected="selected"';
						echo '>'.$temp.'</option>';
					}
				?>
				</select><label style="margin-left:20px;" class="description">Your current theme: <b><?php echo get_current_theme(); ?></b></label>
			</td>
		</tr>
		<tr valign="top" id="new-theme-area" style="display:none;">
			<th scope="row">Copy to new name</th>
			<td>
				<input type="text" style="background:#f5f5f5;" disabled="true" value=""/><input id="new-theme-name" name="new-theme-name" type="text" value=""/>
				<label style="margin-left:20px;" class="description">You can type a new name</label>
			</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" id="copy-save" name="save" class="button-primary" value="Copy Theme" /><label id="copy-info-area" style="margin-left:10px;" class="description"></label>
		</p>
	</form>

</div>
<script src="<?php bloginfo('url') ?>/wp-content/plugins/<?php echo PLUGIN_NAME?>/js/admin.js"></script>
<?php
}
?>
