<?php
/*
	This file is not really nessicary for most use cases. I found this file was needed for 
	runtime parsing, such as BBCode. The functions in 'main_functions.php' should be more than 
	enough for you.
	
	Usage: www.path.to/this/file.php?$url={$image}
*/
require('main_functions.php');
$url = (isset($_GET['url']) && is_string($_GET['url'])) ? stripslashes($_GET['url']) : '';
$image = (@isImage($url));
if ($image) {
	$size=getimagesize($url);
	@header("Content-Type: {$size['mime']}");
	if (strpos($url, 'https://') !== false) {
		$url=parseImage($url);
	}
	else {
		$url=removeFrontTag($url);
		$url=parseImage($url);
	}
	header('Location: '.$url);
	exit;
}