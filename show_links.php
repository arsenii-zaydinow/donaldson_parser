<?php
$filename = 'links.txt';

$data = file_get_contents($filename);
$links = unserialize($data);

foreach ($links as $key => $value) {
	echo $key.'. '.$value.'<br>';
}
?>