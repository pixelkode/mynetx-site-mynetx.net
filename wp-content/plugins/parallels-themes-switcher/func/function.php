<?php

// Author: XhtmllWeaver.com , Sydney, Australia
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

define(SUPPORT_EMAIL, 'wp.support@xhtmlweaver.com');
define(PLUGIN_NAME, 'parallels-themes-switcher');

function WPXW_showErr($ErrMsg,$httperr='HTTP/1.0 500 Internal Server Error') {
	header($httperr);
	header('Content-Type: text/plain;charset=UTF-8');
	echo $ErrMsg;
	exit;
}

$WPXW_cookie_time=get_option('WPXW_Cookie-Time')?(int)get_option('WPXW_Cookie-Time'):3;

if(isset($_COOKIE['WPXW_preview_theme_'.COOKIEHASH])){
	$WPXW_preview_theme=$_COOKIE['WPXW_preview_theme_'.COOKIEHASH];
}

if (! $WPXW_preview_css ){
	$WPXW_preview_css = $WPXW_preview_theme;
}

if(isset($WPXW_preview_theme) && file_exists(get_theme_root() . "/$WPXW_preview_theme")) {
	add_filter('template','WPXW_set_theme');
}

if(isset($WPXW_preview_css) && file_exists(get_theme_root() . "/$WPXW_preview_css")) {
	add_filter('stylesheet','WPXW_set_css');
}
function WPXW_check_Permissions(){
	if(get_option('WPXW_only_admin')){
		if(is_user_logged_in()){
			return true;
		}elseif(get_option('WPXW_excluded_ip')){
			$ips=preg_split("/[,\s\|\n\r]+\s*/", get_option('WPXW_excluded_ip'));
			if(getenv('HTTP_CLIENT_IP')){
				$client_ip = getenv('HTTP_CLIENT_IP');
			} elseif(getenv('HTTP_X_FORWARDED_FOR')) {
				$client_ip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif(getenv('REMOTE_ADDR')) {
				$client_ip = getenv('REMOTE_ADDR');
			} else {
				$client_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
			}
			foreach($ips as $ip){
				if($client_ip === $ip){
					return true;
				}
			}
		}
	}
	return false;
}

function WPXW_set_theme($themename) {
	global $WPXW_preview_theme;
	return $WPXW_preview_theme;
}

function WPXW_set_css($cssname) {
	global $WPXW_preview_css;
	return $WPXW_preview_css;
}
function WPXW_action(){
	$jsonArr=array();
	if($_GET['action'] == 'WPXW_getAllThemes'){
		global $WPXW_preview_theme;
		$themes=get_themes();
		if($WPXW_preview_theme){
			$current=$WPXW_preview_theme;
		}else{
			$current=get_current_theme();
		}
		$themeArr=array();
		foreach($themes as $theme){
			$name=$theme['Name'];
			$title=$theme['Title'];
			$temp=$theme['Template'];
			$author=$theme['Author Name'];
			$version=$theme['Version'];
			$screenshot=$theme['Theme Root URI'].'/'.$temp.'/'.$theme['Screenshot'];
			$themeArr[]=array('name'=>$name,
				'template'=>$temp,
				'title'=>$title,
				'author'=>$author,
				'version'=>$version,
				'screenshot'=>$screenshot
			);
		}
		$jsonArr=array('current'=>$current, 'allthemes'=>$themeArr);
		echo json_encode($jsonArr);die();
	}elseif($_GET['action'] == 'WPXW_switchTheme'){
		if(!WPXW_check_Permissions()){
			WPXW_showErr('You have No Permissions!');
		}
		$theme=$_GET['theme'];global $WPXW_cookie_time;
		if(empty($theme))WPXW_showErr('Theme is emtpy.');
		setcookie('WPXW_preview_theme_'.COOKIEHASH, $theme, time()+60*60*24*$WPXW_cookie_time, COOKIEPATH, COOKIE_DOMAIN);
		echo 'success!';die();
	}
}
add_action('init', 'WPXW_action');


add_action('admin_menu', 'WPXW_add_options');
function WPXW_add_options() {
	add_options_page('Parallels Theme Switcher options','Parallels Theme Switcher', 8, __FILE__, 'WPXW_the_options');
}

function WPXW_addScript(){
	$css = '<link rel="stylesheet" href="' .get_bloginfo("wpurl") . '/wp-content/plugins/' . PLUGIN_NAME . '/css/smoky-parallels.css" type="text/css" media="screen" />';
	$script = '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/' . PLUGIN_NAME . '/js/smoky-parallels.js"></script>';
	echo $css . $script;
}

function WPXW_add_action(){
	if(WPXW_check_Permissions()){

		if(!get_option("WPXW_file")||get_option("WPXW_file")=='head'){
			add_action ('wp_head', 'WPXW_addScript');
		}elseif(get_option("WPXW_file")=='foot'){
			add_action ('wp_footer', 'WPXW_addScript');
		}

	}
}
add_action('init', 'WPXW_add_action');

function WPXW_copy_dir($dirFrom, $dirTo){
    if(!is_writable($dirFrom)||is_file($dirTo)){  
       return 'can not create '.$dirTo; 
    }
	$handle=@opendir($dirFrom);//try to open the 'from' directory
	if(!$handle){//open failed
		return 'No such directory!';
	}
    if(!file_exists($dirTo)){  
        mkdir($dirTo);  //create new dir
    } else {
		return 'You have copied it!';
	}
	while ($file = readdir($handle)) {  
		if ($file == '.' || $file == '..' || $file == 'CVS' || $file==".svn" || $file=='.git')continue;
		$fileFrom = $dirFrom . DIRECTORY_SEPARATOR .$file;  
		$fileTo = $dirTo .DIRECTORY_SEPARATOR .$file;  
		if(is_dir($fileFrom)){
			WPXW_copy_dir($fileFrom,$fileTo);
		} else {
			@copy($fileFrom,$fileTo);  
		}  
	}
	return true;
}
function WPXW_copy_new_theme($old, $new){
	$theme_root = get_theme_root();
	$olddir=$theme_root.DIRECTORY_SEPARATOR .$old;
	$newdir=$theme_root.DIRECTORY_SEPARATOR .$new;
	$is=WPXW_copy_dir($olddir, $newdir);
	if($is === true){
		echo '{<code><span style="color:red;">' . $old . '</code> has been duplicated as <code><span style="color:red;">'.$new.'</span></code>}';
	}else{
		echo '<code><span style="color:red;">ERRROR: Cannot duplicate theme '.$newdir.', make sure your theme directory is writable or ' . $newdir . ' does not exist on the server. You can also contact ' . SUPPORT_EMAIL . ' for the support.</span></code>';
	}
}
?>
