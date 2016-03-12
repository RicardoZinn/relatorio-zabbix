<?php
$nome_fonte = $argv[0];
$url = $argv[1];
$unique = $argv[2];

createLocalImage($url,$unique);

function createLocalImage($url,$unique){
	$extension = ".png";
	$filename  = $unique . $extension;
	$path      = "images/";
	
	$img = @imagecreatefrompng($url);
	imagepng($img, $path . $filename);
	chown($path . $filename ,"apache");
}


?>