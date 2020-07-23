<?php

	$url = "https://shop.donaldson.com/store/ru-ru/search?N=1447732658&Nr=product.language%3ARussian&catNav=true&st=parts";
	$links[1] = $url;

	$no = 20;

	for ($i=2; $i < 689; $i++) { 
		
		$link = $url.'&No='.strval($no);

		$links[$i] = $link;

		$no = $no + 20;

		/*if ($i % 50 == 0) {
			echo $i."<br>";
		}*/

	}

	$filename = 'links.txt';

	$data = serialize($links);

	file_put_contents($filename, $data);

?>