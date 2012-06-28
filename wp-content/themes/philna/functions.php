<?php

session_start();

load_theme_textdomain('philna',get_template_directory() . '/lang');
add_action('admin_menu', array('philnaOptions', 'add'));
/** widgets */
require_once("admin/widgets.php");
require_once("admin/settings.php");

remove_action('wp_head', 'wp_generator');
if(!is_admin()) wp_deregister_script('jquery');

function the_post_source_host($strUrl) {
	$arrUrl = @parse_url($strUrl);
	if(is_array($arrUrl)) {
		$strHostFriendly = $strHost = str_replace('www.', '', $arrUrl['host']);
		switch($strHost) {
			case 'liveside.net':
				$strHostFriendly = 'LiveSide.net';
				break;
			case 'livesino.net':
				$strHostFriendly = 'LiveSino.net';
				break;
		}
		return array('host' => $strHost, 'friendly' => $strHostFriendly);
	}
	return false;
}

function the_post_source_icon($strStart, $strEnd) {
	echo get_the_post_source_icon($strStart, $strEnd);
}

function get_the_post_source_icon($strStart, $strEnd) {
	global $post;
	$strIcon = '';
	$strSource = get_post_meta($post->ID, 'translatedfrom', true);
	if($strSource) {
		//list($strSource) = explode('?', trim(strtolower($strSource))); // remove query params
		if($arrHost = the_post_source_host($strSource)) {
			$strImageUrl = get_bloginfo('template_url').'/img/source/'.$arrHost['host'].'.png';
			$strImageFile = substr($strImageUrl, strlen(get_bloginfo('wpurl')) + 1);
			if(file_exists($strImageFile)) {
				$arrImage = getimagesize($strImageFile);
				$strImage = '<img src="'.cdn($strImageUrl).'" width="'.$arrImage[0].'" height="'.$arrImage[1].'" '.
					'alt="'.__('Translated from').' '.$arrHost['friendly'].'" title="'.__('Translated from').' '.$arrHost['friendly'].'" />';
				$strIcon = $strStart.'<a href="'.htmlentities($strSource).'">'.$strImage.'</a>'.$strEnd;
			}
		}
	}
	else {
		$strSource = get_post_meta($post->ID, 'source', true);
		if($strSource) {
			//list($strSource) = explode('?', trim(strtolower($strSource))); // remove query params
			if($arrHost = the_post_source_host($strSource)) {
				$strImageUrl = get_bloginfo('template_url').'/img/source/'.$arrHost['host'].'.png';
				$strImageFile = substr($strImageUrl, strlen(get_bloginfo('wpurl')) + 1);
				if(file_exists($strImageFile)) {
					$arrImage = getimagesize($strImageFile);
					$strImage = '<img src="'.cdn($strImageUrl).'" width="'.$arrImage[0].'" height="'.$arrImage[1].'" '.
						'alt="'.__('Source').': '.$arrHost['friendly'].'" title="'.__('Source').': '.$arrHost['friendly'].'" />';
					$strIcon = $strStart.'<a href="'.htmlentities($strSource).'">'.$strImage.'</a>'.$strEnd;
				}
			}
		}
	}
	return $strIcon;
}

function get_the_post_source($strStart, $strEnd) {
	global $post;
	if($strSource = get_post_meta($post->ID, 'translatedfrom', true)) {
		//list($strSource) = explode('?', trim(strtolower($strSource))); // remove query params
		if($arrHost = the_post_source_host($strSource)) {
			return '<br /><br />'.__('Translated from').': '.$strStart.get_the_post_source_icon('', '').
				' <a href="'.htmlentities($strSource).'">'.$arrHost['friendly'].'</a>'.$strEnd;
		}
	}
	else if($strSource = get_post_meta($post->ID, 'source', true)) {
		//list($strSource) = explode('?', trim(strtolower($strSource))); // remove query params
		return '<br /><br />'.__('Source').': '.$strStart.get_the_post_source_icon('', '').
			' <a href="'.htmlentities($strSource).'">'.htmlentities($strSource).'</a>'.$strEnd;
	}
}

function the_post_source($strStart, $strEnd) {
	echo get_the_post_source($strStart, $strEnd);
}

function the_post_badge($strStart, $strEnd) {
	echo get_the_post_badge($strStart, $strEnd);
}

function get_the_post_badge($strStart, $strEnd) {
	global $post;
	$strBadge = '';
	$arrTags = get_the_tags();
	if($arrTags) {
		foreach($arrTags as $objTag) {
			$strImageUrl = get_bloginfo('template_url').'/img/badges/'.strtolower(str_replace(' ', '', $objTag->name)).'.png';
			$strImageFile = substr($strImageUrl, strlen(get_bloginfo('wpurl')) + 1);
			if(file_exists($strImageFile)) {
				$arrImage = getimagesize($strImageFile);
				$strImage = '<img src="'.cdn($strImageUrl).'" width="'.$arrImage[0].'" height="'.$arrImage[1].'" '.
					'alt="'.$objTag->name.'" title="'.$objTag->name.'" />';
				$strBadge = $strStart.'<a href="'.get_tag_link($objTag->term_id).'">'.$strImage.'</a>'.$strEnd;
				break;
			}
		}
	}
	return $strBadge;
}

$arrReadspeak = array(
	'de' => '2ed30d20977fcba433749f91ac1bf21b', // readspeak-de@random.mynetx.net
	'en' => 'cd1482a910d3ebd8b8b4bd897ebc4a62', // mynetx@gmx.de
	'es' => 'd8ad4f6dad205a49781bf9485929a042', // readspeak-es@random.mynetx.net
	'fr' => '91ad858f61378182d500017d8b9ef413', // readspeak-fr@random.mynetx.net
	'it' => '65604d19eaebfd8de236744acbf0c4c3', // readspeak-it@random.mynetx.net
	'nl' => '6c791586b9251b1fe1f775b87e908190', // readspeak-nl@random.mynetx.net
	'pt' => '21f4d46f1ef97d22c25a7257a6166c85' // readspeak-pt@random.mynetx.net
);
if(isset($arrReadspeak[__('en')])) {
	define('READSPEAK_UID', $arrReadspeak[__('en')]);
}

if(isset($_POST['hide_language_bar'])) {
	$_SESSION['hide_language_bar'] = true;
	die();
}

if ( function_exists('add_theme_support') )
	add_theme_support('post-thumbnails');

?>