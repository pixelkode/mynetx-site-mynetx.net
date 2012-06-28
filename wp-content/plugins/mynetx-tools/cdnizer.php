<?php

if (!isset($_GET['url'])) die();
$ch = curl_init(str_replace(':/', '://', $_GET['url']));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
$data = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
if ($http != 200) die();
if ($type) header('Content-Type: '.$type);
header('ETag: '.md5($data));
die($data);
