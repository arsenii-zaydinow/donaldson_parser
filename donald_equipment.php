<?php

	//Установка библиотеки phpMorphy
	require_once( 'libs/phpmorphy/src/common.php');
	$dir = 'libs/phpmorphy/dicts';
	$lang = 'ru_RU';
	$opts = array(
    	'storage' => PHPMORPHY_STORAGE_FILE,
	);
	try {
    	$morphy = new phpMorphy($dir, $lang, $opts);
	} catch(phpMorphy_Exception $e) {
    	die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
	}

	//Парсинг оборудования по полученным id на сайте shop.donaldson.com и занесение его в базу данных
	set_time_limit(1000);

	$servername = "localhost";
	$database = "equipment";
	$username = "root";
	$password = "";

	//Функция ucfirst для кириллицы
	mb_internal_encoding("UTF-8");
	function mb_ucfirst($text) {
    	return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
	}

	$conn = mysqli_connect($servername, $username, $password, $database);

	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}
	else {
		echo "Connected successfully!"."<br>";
	}

	$dir   = 'donald_ids/';
	$files = array_diff(scandir($dir), array('..', '.'));

	$i = 0;

	natsort($files);

	foreach ($files as $key => $value) {
		$details[$i] = $files[$key];
		$i++;
	}
	
	$cookiefile = __DIR__ . "/cookie.txt";
	$headers = [
		'Accept-Language: ru-RU,ru;q=0.9,en;q=0.8'
		];
	$donaldLink = 'https://shop.donaldson.com/store/ru-ru/home';
	$url = 'https://shop.donaldson.com/store/rest/fetchproductequipmentlist?id=';
		
	for ($r = 5; $r < /*count($details)*/6; $r++) {
		
		$filename = $dir.$details[$r];
		$data = file_get_contents($filename);
		$data = preg_replace_callback('!s:\d+:"(.*?)";!s', function($m) { return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; }, $data);
		$parts = unserialize($data);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_URL, $donaldLink);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_exec($ch);

		curl_setopt($ch, CURLOPT_URL, 'https://dpm.demdex.net/id?d_visid_ver=4.4.1&d_fieldgroup=MC&d_rtbd=json&d_ver=2&d_orgid=211631365C190F8B0A495CFC%40AdobeOrg&d_nsid=0&ts=1598434813132');
		curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, 'https://col.eum-appdynamics.com/eumcollector/beacons/browser/v1/AD-AAB-AAE-YFM/adrum');
		curl_exec($ch);
		
		
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {
		
			$headers = [
			'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
			'service_token: 3TuSgTm3MyS9ZL0adPDjYg=='
			];

			for ($f = 0; $f < count($parts);$f++) {

				$tableEqp = array();

				$decodedId = urlencode(trim($parts[$f]["id"]));
				$link = $url.$decodedId;

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
				curl_setopt($ch, CURLOPT_URL, $link);
				$html = curl_exec($ch);

				/*echo $parts[$f]["id"];
				echo $html;
				break;*/
			
				if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {
					$json = json_decode($html, TRUE);
					$parseEqp = $json['equipmentList'];

					if (count($parseEqp) > 0) {

						for($h = 0; $h < count($parseEqp); $h++){

							
							//Проверка на наличие производителя двигателя
							if ($parseEqp[$h]['engineMakeDisplayName'] == '-') {
								$engineModel = $parseEqp[$h]['equipmentMakeDisplayName'].' '.$parseEqp[$h]['equipmentEngineModel'];
							}
							else {
								$engineModel = $parseEqp[$h]['equipmentEngineModelSortableValue'];
							}

							//Проверка на наличие доп. опций двигателя
							if ($parseEqp[$h]['equipmentEngineOptions'] != '-') {
								$engineModel = $engineModel.' ('.$parseEqp[$h]['equipmentEngineOptions'].')';
							}

							//Оборудование во множетсвенном числе
							// $eqpType = $morphy->castFormByGramInfo(mb_strtoupper($parseEqp[$h]['equipmentTypeDisplayName'], 'UTF-8'), null, array('МН', 'ИМ'), false);

							//Тип оборудования с большой буквы
							$eqpType = mb_ucfirst(mb_strtolower($parseEqp[$h]['equipmentTypeDisplayName'], 'UTF-8'));

							//Заносим данные во временную таблицу
							$temp = "INSERT INTO temp (art, producer, eqpModel, eqpType, engineModel) VALUES ('".mysqli_real_escape_string($conn, trim($parts[$f]['art']))."', '".mysqli_real_escape_string($conn, $parseEqp[$h]['equipmentMakeDisplayName'])."', '".mysqli_real_escape_string($conn, $parseEqp[$h]['equipmentModel'])."', '".mysqli_real_escape_string($conn, $eqpType)."', '".mysqli_real_escape_string($conn, $engineModel)."')";

							if (mysqli_query($conn, $temp)) {
      						//echo "New record created successfully <br>";
							} else {
     						echo "Error: " . $temp . "<br>" . mysqli_error($conn);
     						break 3;
							}

						}

						//Удаляем повторения из временной таблицы
						$delete = "DELETE t1.* FROM temp AS t1 LEFT JOIN (SELECT id FROM temp GROUP BY art, producer, eqpModel, eqpType, engineModel) AS t2 ON t1.id = t2.id WHERE t2.id IS NULL";

						if (mysqli_query($conn, $delete)) {
      						//echo "New record created successfully <br>";
						} else {
     						echo "Error: " . $delete . "<br>" . mysqli_error($conn);
     						break;
						}

						//Заносим уникальные значения в основную таблицу
						$sql = "INSERT INTO equipment (art, producer, eqpModel, eqpType, engineModel) SELECT art, producer, eqpModel, eqpType, engineModel FROM temp";

						if (mysqli_query($conn, $sql)) {
      						//echo "New record created successfully <br>";
						} else {
     						echo "Error: " . $sql . "<br>" . mysqli_error($conn);
     						break;
						}

						//Очищаем временную таблицу
						$truncate = "TRUNCATE TABLE temp";

						if (mysqli_query($conn, $truncate)) {
      						//echo "New record created successfully <br>";
						} else {
     						echo "Error: " . $truncate . "<br>" . mysqli_error($conn);
     						break;
						}
					}
				}
				else {
					echo 'Не удалось запросить оборудование со страницы '.$link."<br>";
					echo "Код ошибки: ".curl_getinfo($ch, CURLINFO_HTTP_CODE)."<br>";
					echo curl_getinfo($ch, CURLINFO_HEADER_OUT);
					break 2;
				}
				//break;
			}
		}
		else {
			echo 'Не удалось получить cookie со страницы '.$donaldLink;
			break;
		}
		//break;
	}

	curl_close ($ch);
	mysqli_close($conn);
	
?>
