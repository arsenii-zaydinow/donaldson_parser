<?php 
	
	require ('../phpQuery.php');
	require ('get_pages.php');
	require ('get_parts.php');


	$ch = curl_init();

	$url = "https://shop.donaldson.com/store/ru-ru/search?N=3718375764&catNav=true";

	$tables = get_pages($url, $ch);

	get_parts($tables);

	curl_close ($ch);
?>