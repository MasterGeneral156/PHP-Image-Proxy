<?php
/*
	URL to the image. Verifying the image is legitimate is a bit outside of the scope 
	of this repository.
*/
function parseImage($url)
{
	//If image is https://, lets load it directly to save the proxy some traffic. Plus,
	//it'll still show secure in browser.
	if (strpos($url, 'https://') !== false) {
		return $url;
	}
	//Image is not https://, so, lets remove the front-tag and then load it through the proxy.
	else {
		$url = removeFrontTag($url);
		//Will attempt to load through the proxy, if it fails, it'll just load the original image.
		return "https://images.weserv.nl/?url={$url}&errorredirect=ssl:{$url}";
	}
}
/*
	Function to remove the protocol from the URL, since the image proxy does not 
	like them. This also adds "www." to make it a valid URL according to the image 
	proxy.
	Returns url without protocol tag, and www. added.
*/
function removeFrontTag($url)
{
	$url = str_replace("http://","",$url);
	$url = str_replace("https://","",$url);
	$url = str_replace("www.","",$url);
	$url = "www.{$url}";
	return $url;
}
/*
	Function to test if the input URL is an image.
*/
function isImage($url)
{
    $params = array('http' => array('method' => 'HEAD'));
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp)
        return false;  // Problem with url
    $meta = stream_get_meta_data($fp);
    if ($meta === false) {
        fclose($fp);
        return false;  // Problem reading data from url
    }
    $wrapper_data = $meta["wrapper_data"];
    if (is_array($wrapper_data)) {
        foreach (array_keys($wrapper_data) as $hh) {
            if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") // strlen("Content-Type: image") == 19
            {
                fclose($fp);
                return true;
            }
        }
    }
    fclose($fp);
    return false;
}