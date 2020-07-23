<?php
function get_equipment (/*$parts,*/ $ch, $cookiefile = __DIR__ . "/cookie.txt") {

	$filename = 'parts.txt';
	$data = file_get_contents($filename);
	$parts = unserialize($data);

	$headers = [
		'service_token: 3TuSgTm3MyS9ZL0adPDjYg=='
		];
	//$url = 'https://shop.donaldson.com/store/ru-ru/product/P103688/14618';

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
	//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_URL, $url);

	foreach ($parts as $key => $value) {

	$url = "https://shop.donaldson.com/store/rest/fetchproductequipmentlist?id=".$parts[2]["id"];
	curl_setopt($ch, CURLOPT_URL, $url);
		
	$html = curl_exec($ch);
	$json = json_decode($html, TRUE);
	$arr = $json['equipmentList'];
	//echo var_dump($arr);
	//echo $json[1];
	break;
	}

	for($i = 0; $i < count($arr); $i++){
		foreach ($arr[$i] as $key => $value) {
			echo $key." | ";
			//break;
		}
		echo "<br>";
		break;
	}

	for($i = 0; $i < count($arr); $i++){
		foreach ($arr[$i] as $key => $value) {
			echo $value." | ";
		}
		echo "<br>";
		//break;
	}


}

$ch = curl_init();
get_equipment($ch);

?>