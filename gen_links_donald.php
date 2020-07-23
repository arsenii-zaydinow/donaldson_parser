<?php
	require ('../phpQuery.php');
	set_time_limit(500);

	$cookiefile = __DIR__ . "/cookie.txt";
	$header = ['accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-encoding: gzip, deflate, br',
	'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
	'cache-control: max-age=0',
	'upgrade-insecure-requests: 1'];
	$url = 'https://shop.donaldson.com/store/include/search/autoSuggest.jsp?Dy=1&collection=/content/Shared/Auto-Suggest%20Panels&Ntt=';

	$dir    = 'rs_parts/';
	$files = array_diff(scandir($dir), array('..', '.'));

	$i = 0;

	natsort($files);

	foreach ($files as $key => $value) {
		$o[$i] = $files[$key];
		$i++;
	}

	$i = 0;

	for ($r = 3; $r < 4; $r++) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
		//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt($ch, CURLOPT_URL, 'https://shop.donaldson.com/store/ru-ru/home');
		$returned = curl_exec($ch);
		curl_close ($ch);

		$list = $o[$r];
		$filename = 'rs_parts/'.$o[$r];
		$data = file_get_contents($filename);
		$arts = unserialize($data);

		$header = ['accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
		'accept-encoding: gzip, deflate, br',
		'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
		'cache-control: max-age=0'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		//curl_setopt($ch, CURLOPT_HEADER, true);

		for ($f = 254; $f < /*count($arts)*/255; $f++) {

			$link = strval($url.trim($arts[$f]));
			curl_setopt($ch, CURLOPT_URL, $link);
			$returned = curl_exec($ch);
			//$json = phpQuery::newDocument($returned);
			//echo $returned;
			$decoded = gzdecode($returned);
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {

				$document = json_decode($decoded);

				/*echo $arts[$f].'<br>';
				echo var_dump($document).'<br>';
				foreach ($document as $key => $value) {
						echo $key.'<br>';
				}*/

				if (array_key_exists('records', $document->contents[0]->autoSuggest[1])) {
					if (array_key_exists('pdpUrl', $document->contents[0]->autoSuggest[1]->records[0]->attributes)) {
						$id_response = pathinfo($document->contents[0]->autoSuggest[1]->records[0]->attributes->pdpUrl[0]);
						$id = $id_response['filename'];
						$parts[$i]['art'] = $arts[$f];
						$parts[$i]['id'] = $id;
						$i++;
					}
					else {
						echo "У детали нет ссылки: ".$arts[$f].' '.curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br>';
					}
				}
				else {
					echo "У детали нет ссылки: ".$arts[$f].' '.curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br>';
				}
				
			}
			else {
			echo "Ошибка при запросе ссылки! <br>";
			echo "Код ошибки: ".curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br>';
			echo "Страницы: ".$list.'<br>';
			echo "Деталь: ".$arts[$f].'<br>';
			echo "Ключ: ".$f.'<br>';
			echo curl_getinfo($ch, CURLINFO_HEADER_OUT);
			echo $returned;
			break;
			}
				//break;
				//time_nanosleep (1, 0);
			}
		curl_close ($ch);

		$files = 'donald_ids/'.$list;
		$info = serialize($parts);
		file_put_contents($files, $info);
		$i = 0;

		break;
		}
		

	

	/*foreach ($parts as $key => $value) {
		echo $key.'. '.$parts[$key]['art'].' | '.$parts[$key]['id'].'<br>';
	}*/



	/*foreach ($document as $key => $value) {
		echo $key.'<br>';
	}*/

	//$arr = $document['contentCollection'];
	//echo var_dump($document);
	//$document->contents;

	/*foreach ($document->contents[0]->autoSuggest[1] as $key => $value) {
		echo $key.'<br>';
	}*/
	//echo var_dump($document->contents[0]->autoSuggest[1]).'<br>';

	/*if (array_key_exists('records', $document->contents[0]->autoSuggest[1])) {
		echo "Ссылка есть";
	}
	else {
		echo "сылки нет";
	}*/

	/*$link = $document->contents[0]->autoSuggest[1]->records[0]->attributes->pdpUrl[0].'<br>';
	echo $link.'<br>';
	$copms = pathinfo($link);
	echo $copms['filename'];
	foreach ($copms as $key => $value) {
		echo $key;
	}*/

	//echo get_class_methods($document->contents[0]->autoSuggest[1]);

	/*foreach (get_object_vars($document->contents[0]->autoSuggest[1]->records[0]) as $key => $value) {
		echo $key."<br>";
	}*/

	//echo gettype($document->contents[0]);

	/*for($i = 0; $i < count($arr); $i++){
		foreach ($arr[$i] as $key => $value) {
			echo $key." | ";
			//break;
		}
		echo "<br>";
		break;
	}*/

?>