<?php

	require_once('wp-config.php');
	mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Error: Can\'t connect to database server.');
	mysql_select_db(DB_NAME) or die('Error: Can\'t connect to database.');

	$postid = basename($_SERVER['REQUEST_URI']);
	$query = mysql_query('SELECT language_code FROM mx_icl_translations WHERE element_id = "'.addslashes($postid).'" AND element_type = "post_post"');
	if(!mysql_num_rows($query)) {
		header('Location: http://mynetx.net/');
		die();
	}
	$data = mysql_fetch_row($query);
	$language = $data[0];
	if($language == "en") {
		$url = "http://mynetx.net";
	}
	else {
		$query = mysql_query('SELECT option_value FROM mx_options WHERE option_name = "icl_sitepress_settings"');
		$data = mysql_fetch_row($query);
		$settings = unserialize(utf8_encode($data[0]));
		$url = $settings['language_domains'][$language];
	}
	$query = mysql_query('SELECT post_name FROM mx_posts WHERE ID = "'.$postid.'"');
	if(!mysql_num_rows($query)) {
		header('Location: http://mynetx.net/');
		die();
	}
	$data = mysql_fetch_row($query);
	$postname = $data[0];

	header('Location: '.$url."/".$postid."/".$postname);
	die();
?>
