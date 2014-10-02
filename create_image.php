<?php
	//Background image.
	$handle = fopen('http://babbage.cs.missouri.edu/~rchcp5/Test-Image-Generator/backgrounds/woods4.jpg', 'rb');
	
	$img = new Imagick();
	$img->readImageFile($handle);
	$img->resizeImage(500, 500, 0, 0);
	$img->writeImage('backgrounds/woods4.jpg');
	
	header( "Content-Type: image/jpeg" );
	echo $img;
?>