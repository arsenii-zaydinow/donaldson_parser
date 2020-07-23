<?php 

	require ('get_pages.php');
	require ('get_parts.php');


	$ch = curl_init();

	$url = "https://shop.donaldson.com/store/ru-ru/search?N=3718375764&Nr=product.language%3ARussian&catNav=true&st=parts";

	$tables = get_pages($url, $ch);

	get_parts($tables);

	curl_close ($ch);
?>