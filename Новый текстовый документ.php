<?php
$filename = 'parts.txt';

$data = file_get_contents($filename);
$bookshelf = unserialize($data);

foreach ($bookshelf as $key => $value) {
	echo $key.". ".$bookshelf[$key]["art"]." ".$bookshelf[$key]["desc"]." ".$bookshelf[$key]["id"]."<br>";
}
?>