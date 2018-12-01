<?php
	require_once('wp-config.php');
	mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Error: Can\'t connect to database server.');
	mysql_select_db(DB_NAME) or die('Error: Can\'t connect to database.');

	$postid = substr(basename($_SERVER['REQUEST_URI']), 1);
	$query = mysql_query('SELECT filename FROM mx_download_monitor_files WHERE id='.$postid);
	if(!mysql_num_rows($query)) {
		header('Location: http://mynetx.net/');
		die();
	}
	$data = mysql_fetch_row($query);
	$file = basename($data[0]);
	header('Location: http://mynetx.net/download/'.$file);
	die();
?>
