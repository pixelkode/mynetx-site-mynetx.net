<?php

function cdnizer($strPath) {
	return 'http://cdn.mynetx.net/wp-content/plugins/mynetx-tools/cdnizer/'.rawurlencode($strPath);
}

header('Content-Type: text/javascript');
header('Expires: '.date('r', time() + 28*86400));
ob_start('ob_gzhandler');
define('HOST', $_GET['host']);

$strCache = 'scripts-min.'.HOST.'.js';
$strCache = 'scripts-min.js';
if(file_exists($strCache)) {
	die(file_get_contents($strCache));
}

$arrIncludes = array();
ob_start();

include('jquery-1.4.4.min.js');
echo 'var $j = jQuery, $ = $j;';
include('jquery.address.js');
include('bing.config.js');
include('bing.1.03.js');
//include('flowplayer.js');
//include('twitterwidgets.js');
//include('fbshare.js');
include('reinvigorate.js');
//include('xfbml.js');
include('google-analyticator.js');
include('_main.js');
include('yinheli.js');
//include('snow-storm.js');

// compile
$strText = ob_get_contents();
ob_end_clean();
$resCurl = curl_init('http://closure-compiler.appspot.com/compile');
curl_setopt($resCurl, CURLOPT_POST, 1);
curl_setopt($resCurl, CURLOPT_POSTFIELDS, 'js_code='.rawurlencode($strText).'&compilation_level=SIMPLE_OPTIMIZATIONS&output_format=text&output_info=compiled_code');
curl_setopt($resCurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($resCurl, CURLOPT_HEADER, 0);
$strCompiled = curl_exec($resCurl);

// add meta
$strYear = date('Y');
$strVersion = date(sprintf('5.1.%02sd.Hi', (12 * (date('Y')) + date('m') - 24104)));

// include copyrights
$strIncludes = '';
foreach($arrIncludes as $arrInclude) {
	$strIncludes .= "\n * Includes ".$arrInclude[0].", Copyright (c) ".$arrInclude[1].'.';
	if(isset($arrInclude[2]))
		$strIncludes .= "\n *     ".$arrInclude[2];
}
if($strIncludes != '')
	$strIncludes = "\n *   " . $strIncludes;

$strCompiled = <<< HEADER
/* Partial copyright (c) 2008-$strYear mynetx Creations.
 * Version $strVersion
HEADER
.$strIncludes."\n */\n\n".$strCompiled;

// store cache
$resCache = fopen($strCache, 'w');
fwrite($resCache, $strCompiled);
fclose($resCache);

echo $strCompiled;
