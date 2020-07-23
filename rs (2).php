<?php 
	
	require ('../phpQuery.php');

	$url = "https://rs-power.ru/zapasnyie-chasti/donaldson/";
	//$referer = "https://shop.donaldson.com/store/ru-ru/home";
	$header [] = "Accept-Language: ru-RU,ru;q=0.9,en;q=0.8";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
	$returned = curl_exec($ch);
	
	
	$document = phpQuery::newDocument($returned);

	echo $document;
	
	/*$paths = $document->find('table.searchListPartTable');

	foreach ($paths->find('input#product_url') as $key => $value) {
		$pq = pq($value);
		$products[$key]["href"] = "https://shop.donaldson.com".$pq->attr("value");
		//echo "https://shop.donaldson.com". $pq->attr("value") . "<br>";
	}*/

	/*foreach ($products as $key => $value) {
		curl_setopt($ch, CURLOPT_URL, $products[$key]["href"]);
		$returned = curl_exec($ch);
		$document = phpQuery::newDocument($returned);
		echo $document;
		$art = $document->find('#productPageProductNumber');
		$data[$key]['art'] = pq($art)->text();
		$name = $document->find('.prodSubTitle');
		$data[$key]['name'] = pq($name)->text();
		$tech = $document->find('.applicationPartSectionDiv');
		//echo $tech;
		if (is_null ( $tech ) == true) {
    	$data[$key]['tech'] = "Нет оборудования";}
		else {
    	$data[$key]['tech'] = "Есть оборудование";;
		}
		//echo $data[$key]['art'].$data[$key]['name'].$data[$key]['tech']."<br>";

		break;
	}*/
	curl_setopt($ch, CURLOPT_URL, "https://shop.donaldson.com/store/rest/fetchproductequipmentlist?id=11895");
	$headers = [
		'accept' => 'application/json, text/javascript, */*; q=0.01',
		'accept-encoding' => 'gzip, deflate, br',
		'accept-language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
		'adrum' => 'isAjax:true',
		'content-length' => '14',
		'content-type' => 'application/JSON',
		'origin' => 'https://shop.donaldson.com',
		'referer' => 'https://shop.donaldson.com/store/ru-ru/product/DBL7405/11895',
		'sec-fetch-dest' => 'empty',
		'sec-fetch-mode' => 'cors',
		'sec-fetch-site' => 'same-origin',
		'service_token' => '3TuSgTm3MyS9ZL0adPDjYg==',
		'x-requested-with' => 'XMLHttpRequest',
		'cookie' => 'DCI_COOKIE_PERMISSION=RU; _gcl_au=1.1.1185264108.1592901664; _ga=GA1.2.487174083.1592901686; velaro_visitorId=%22eCTHNb1K30eanRF5hR195g%22; _ga=GA1.3.487174083.1592901686; _gcl_aw=GCL.1592902629.Cj0KCQjw0Mb3BRCaARIsAPSNGpXXPIxRQHG76GetO3Jp4PchBnnvjmsTyovmqSHwYhwmBqQzF-nI5jYaAoZzEALw_wcB; _gac_UA-5402515-1=1.1592902629.Cj0KCQjw0Mb3BRCaARIsAPSNGpXXPIxRQHG76GetO3Jp4PchBnnvjmsTyovmqSHwYhwmBqQzF-nI5jYaAoZzEALw_wcB; _gac_UA-5402515-1=1.1592902629.Cj0KCQjw0Mb3BRCaARIsAPSNGpXXPIxRQHG76GetO3Jp4PchBnnvjmsTyovmqSHwYhwmBqQzF-nI5jYaAoZzEALw_wcB; JSESSIONID="ef9-NruQjkphm2SV0n30UTV8HJizC_4uFUa4F7GK.gdcplecatg02:store-server-2"; AKA_A2=A; _fileDownloaded=false; AMCVS_211631365C190F8B0A495CFC%40AdobeOrg=1; AMCV_211631365C190F8B0A495CFC%40AdobeOrg=1075005958%7CMCIDTS%7C18447%7CMCMID%7C70451477280615329474141761442021645696%7CMCAAMLH-1594377840%7C6%7CMCAAMB-1594377840%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1593780240s%7CNONE%7CvVersion%7C4.4.1; _hjid=73b3f42f-fef7-43f3-b770-35a59697865d; _hjIncludedInSample=1; BIGipServerGDC_DMZ_dciorigin-shop=417426338.36895.0000; s_cc=true; _hjAbsoluteSessionInProgress=1; CartCount=1-9; _gid=GA1.2.1382085841.1593773061; velaro_endOfDay=%222020-07-03T23%3A59%3A59.999Z%22; _gid=GA1.3.1382085841.1593773061; _uetsid=b6054a36-44d3-9838-4f74-66f745682e90; _uetvid=c0e51704-8fcc-211b-efe3-48e7fc35041e; gpv_p3=shop%3A%2Fstore%2Fru-ru%2Fproduct%2Fdbl7405%2F11895; s_sq=donaldson-prd%3D%2526c.%2526a.%2526activitymap.%2526page%253Dshop%25253A%25252Fstore%25252Fru-ru%25252Fproduct%25252Fdbl7405%25252F11895%2526link%253D%2525D0%25259F%2525D0%2525BE%2525D0%2525BA%2525D0%2525B0%2525D0%2525B7%2525D0%2525B0%2525D1%252582%2525D1%25258C%252520%2525D0%2525B1%2525D0%2525BE%2525D0%2525BB%2525D1%25258C%2525D1%252588%2525D0%2525B5%2526region%253DBODY%2526pageIDType%253D1%2526.activitymap%2526.a%2526.c%2526pid%253Dshop%25253A%25252Fstore%25252Fru-ru%25252Fproduct%25252Fdbl7405%25252F11895%2526pidt%253D1%2526oid%253D%2525D0%25259F%2525D0%2525BE%2525D0%2525BA%2525D0%2525B0%2525D0%2525B7%2525D0%2525B0%2525D1%252582%2525D1%25258C%252520%2525D0%2525B1%2525D0%2525BE%2525D0%2525BB%2525D1%25258C%2525D1%252588%2525D0%2525B5%2526oidt%253D3%2526ot%253DSUBMIT; ADRUM=s=1593773150380&r=https%3A%2F%2Fshop.donaldson.com%2Fstore%2Fru-ru%2Fproduct%2FDBL7405%2F11895%3F0; ADRUM_BT1="R:116|i:1726369|e:113|d:190"; ADRUM_BTa="R:116|g:c42c5f4b-85aa-4754-81b7-aa53196c09ed|n:donaldsonprod_d74c5c27-4c53-4750-b2f1-b247110cd5af"; sessionExpiry=7200'
	];
	/*curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers );
	$returned = curl_exec($ch);
	$document = phpQuery::newDocument($returned);
	echo $document;*/
	curl_close ($ch);
?>