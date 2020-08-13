<?php
	require ('../phpQuery.php');
	set_time_limit(500);

	$cookiefile = __DIR__ . "/cookie.txt";
	$url = 'https://shop.donaldson.com/store/include/search/autoSuggest.jsp?Dy=1&collection=/content/Shared/Auto-Suggest%20Panels&Ntt=';
	$headers = [
		'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
		'accept-encoding: gzip, deflate, br',
		'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
		'cache-control: max-age=0',
		'sec-fetch-dest: document',
		'sec-fetch-mode: navigate',
		'sec-fetch-site: none',
		'sec-fetch-user: ?1',
		'upgrade-insecure-requests: 1'
		];

	$dir   = 'rs_parts/';
	$files = array_diff(scandir($dir), array('..', '.'));

	$i = 0;

	natsort($files);

	foreach ($files as $key => $value) {
		$o[$i] = $files[$key];
		$i++;
	}

	$i = 0;

	for ($r = 23; $r < 24; $r++) {

		$parts = array();
		$filename = $dir.$o[$r];
		$data = file_get_contents($filename);
		$arts = unserialize($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		//curl_setopt($ch, CURLOPT_HEADER, true);

		curl_setopt($ch, CURLOPT_URL, 'https://shop.donaldson.com/store/ru-ru/home');
		curl_exec($ch);

		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {

		for ($f = 0; $f < count($arts); $f++) {	

			$decodedArt = urlencode(trim($arts[$f]));
			$link = $url.$decodedArt;
			curl_setopt($ch, CURLOPT_URL, $link);

			$returned = curl_exec($ch);

			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {

				$decoded = gzdecode($returned);
				$document = json_decode ($decoded);

				/*echo $decoded.'<br>';

				foreach ($document->contents[0]->autoSuggest[1] as $key => $value) {
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
						echo "У детали нет ссылки, уровень records: ".$arts[$f].' '.curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br>';
					}
				}
				else {
					echo "У детали нет ссылки, уровень pdpUrl: ".$arts[$f].' '.curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br>';
				}
				
			}
			else {
			echo "Ошибка при запросе ссылки! <br>";
			echo $decoded_art.'<br>';
			echo $link.'<br>';
			echo "Код ошибки: ".curl_getinfo($ch, CURLINFO_HTTP_CODE).'<br>';
			echo "Страницы: ".$list.'<br>';
			echo "Деталь: ".$arts[$f].'<br>';
			echo "Ключ: ".$f.'<br>';
			echo curl_getinfo($ch, CURLINFO_HEADER_OUT).'<br>';
			echo $returned;
			break;
			}
				//break;
				//time_nanosleep (1, 0);
			}
		}
		else {
			echo "Произошла ошибка при получние Cookie с главной страницы!";
		}
		curl_close ($ch);

		if (isset($parts)) {
			$files = 'donald_ids/'.$o[$r];
			$info = serialize($parts);
			file_put_contents($files, $info);
		}
		
		$i = 0;

		//break;
		}

?>
