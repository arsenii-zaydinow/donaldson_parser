<?php 
function get_pages ($url, $ch, $cookiefile = __DIR__ . "/cookie.txt") {
	
	
	$header = ['Accept-Language: ru-RU,ru;q=0.9,en;q=0.8'];

	$no = 0;

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
	curl_setopt($ch, CURLOPT_URL, $url);

    $returned = curl_exec($ch);
	$document = phpQuery::newDocument($returned);
	$table = $document->find('table.searchListPartTable tbody');
	$pq = pq($table);

	$tables = [];
	$tables[0] = $pq;
	$i = 1;

	while (strlen($pq) != 16) {

		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {
		$no = $no + 20;

		$page = $url.'&No='.strval($no);

		curl_setopt($ch, CURLOPT_URL, $page);
		$returned = curl_exec($ch);
		$document = phpQuery::newDocument($returned);
		$table = $document->find('table.searchListPartTable tbody');
		$table->find('.itemNoticeBg')->remove();
		$pq = pq($table);

		if (strlen($pq) != 16) {
			$tables[$i] = $pq;
		}

		$i++;
		echo curl_getinfo($ch, CURLINFO_HTTP_CODE)."<br>";
		//break;
		time_nanosleep (1, rand(200000000, 800000000));
		}
		else {
			echo "Сервер вернул ошибку!". curl_getinfo($ch, CURLINFO_HTTP_CODE);
			break;
		}
		} 
		return $tables;
	}
?>