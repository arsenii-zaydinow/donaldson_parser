<?php
$filename = 'rs_parts/10.txt';

$data = file_get_contents($filename);
$parts = unserialize($data);

foreach ($parts as $key => $value) {
	echo $key.". ".$parts[$key].'<br>';
}
?>