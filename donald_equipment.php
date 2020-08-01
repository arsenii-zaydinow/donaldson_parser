<?php
	$servername = "localhost";
	$database = "equipment";
	$username = "root";
	$password = "";

	$conn = mysqli_connect($servername, $username, $password, $database);

	if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
	}
 
	echo "Connected successfully!"."<br>";


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
		
	for ($r = 0; $r < count($details); $r++) {
		
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
		curl_exec($ch);
		
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {
		
			$headers = [
			'service_token: 3TuSgTm3MyS9ZL0adPDjYg=='
			];

			for ($f = 0; $f < count($parts); $f++) {

				$tableEqp = array();

				$decodedId = urlencode(trim($parts[$f]["id"]));
				$link = $url.$decodedId;

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
				curl_setopt($ch, CURLOPT_URL, $link);
				$html = curl_exec($ch);
			
				if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == "200") {
					$json = json_decode($html, TRUE);
					$parseEqp = $json['equipmentList'];

					if (count($parseEqp) > 0) {
						for($h = 0; $h < count($parseEqp); $h++){

							if ($parseEqp[$h]['engineMakeDisplayName'] == '-') {
								$engineModel = $parseEqp[$h]['equipmentEngineModel'];
							}
							else {
								$engineModel = $parseEqp[$h]['engineMakeDisplayName'].' '.$parseEqp[$h]['equipmentEngineModel'];
							}

							$sql = "INSERT INTO equipment (art, producer, eqpModel, eqpType, engineModel) VALUES ('".trim($parts[$f]['art'])."', '".$parseEqp[$h]['equipmentMakeDisplayName']."', '".$parseEqp[$h]['equipmentModel']."', '".$parseEqp[$h]['equipmentTypeDisplayName']."', '$engineModel')";

							if (mysqli_query($conn, $sql)) {
      							echo "New record created successfully <br>";
							} else {
     							echo "Error: " . $sql . "<br>" . mysqli_error($conn);
							}

						}
					}
				}
				else {
					echo 'Не удалось запросить оборудование со страницы '.$link;
					break 2;
				}

			}
		}
		else {
			echo 'Не удалось получить cookie со страницы '.$donaldLink;
			break;
		}

	}

	curl_close ($ch);
	mysqli_close($conn);
	
?>