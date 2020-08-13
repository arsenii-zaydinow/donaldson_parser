<?php 
	//Получние артикулов запчастей с сайта, для которого нужно получить оборудование
	function get_parts () {

		require ('../phpQuery.php');
		set_time_limit(500);

		$cookiefile = __DIR__ . "/cookie.txt";

		/*$filename = 'links.txt';
		$data = file_get_contents($filename);
		$links = unserialize($data);*/
		$url = 'https://rs-power.ru/zapasnyie-chasti/donaldson/';

		$header = ['Accept-Language: ru-RU,ru;q=0.9,en;q=0.8'];

		$i = 0;


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header );

		for ($n = 231; $n <= 239; $n++) {

			$link = $url.'?page='.$n;
			curl_setopt($ch, CURLOPT_URL, $link);
			$returned = curl_exec($ch);

			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {
			$document = phpQuery::newDocument($returned);
			//$document->find('.itemNoticeBg')->remove();

			/*$table = $document->find('table.searchListPartTable tbody tr');
			$table->find('.itemNoticeBg')->remove();
			$pq = pq($table);*/
			/*foreach ($pq as $key => $value) {
				$parts[]["art"] = $pq[$key]->find(".preStyle")->text();
				$parts[]["id"] = $pq[$key]->find("#product_id")->attr("value");
			}*/
			//echo $document;
			foreach ($document->find('table.products-grid tbody tr') as $key => $value) {
				$pq = pq($value);
				$parts[$i] = $pq->find("td:eq(3)")->text();
				//$parts[$i]["id"] = $pq->find("#product_id")->attr("value");
				//echo $parts[$i]."<br>";
				$i++;
			}

			if ($n % 10 == 0) {
				$filename = 'rs_parts/'.strval($n).'.txt';
				$data = serialize($parts);
				file_put_contents($filename, $data);
				$i = 0;
			}
		time_nanosleep (1, rand(200000000, 800000000));}
		else {
			echo "Ошибка при получении страницы с запчастями!";
			break;
		}
	}
			curl_close ($ch);

			$filename = 'rs_parts/'.strval($n).'.txt';
			$data = serialize($parts);
			file_put_contents($filename, $data);

			//foreach ($parts as $key => $value) {
				//echo $parts[$key]["art"]." ".$parts[$key]["id"]."<br>";
			//}

		/*foreach ($raw_parts as $key => $value) {
			$parts[$key]["art"] = $raw_parts[$key]->find(".preStyle")->text();
			//$parts[$key]["desc"] = $raw_parts[$key]->find(".breakWordTable")->text();
			$parts[$key]["id"] = $raw_parts[$key]->find("#product_id")->attr("value");
		}*/

		/*$filename = 'parts.txt';

		$data = serialize($parts);

		file_put_contents($filename, $data);*/

	}

	get_parts();
?>
