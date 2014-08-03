<?php
function get_page_title($url){

	if( !($data = file_get_contents($url)) ) return false;

	preg_match('/<title.*>(.+)<\/title/',$data,$matches);
    return $matches[1];
}

$url = urldecode($_GET["url"]);
echo get_page_title($url);
//echo "html: " . get_page_title($url);
?>