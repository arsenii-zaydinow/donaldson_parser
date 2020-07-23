<?php 

	function request ($url, $postdata = null, $cookiefile = 'cookie.txt') {
		$ch = curl_init($url);
		$headers = [
		'service_token: 3TuSgTm3MyS9ZL0adPDjYg==',
		'Accept-Language: ru-RU,ru;q=0.9,en;q=0.8'
		];
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
		curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . "/cookie.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
		curl_setopt($ch, CURLOPT_POST, true);

		$html = curl_exec($ch);
		curl_close($ch);
		return $html;
	}

	file_put_contents('cookie.txt', '');

	$html = request('https://shop.donaldson.com/store/rest/fetchproductequipmentlist?id=14716');
	echo $html;
?>

