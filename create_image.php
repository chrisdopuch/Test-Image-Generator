<?php

	//Background image.
	$handle = fopen('backgrounds/woods4.jpg', 'rb');
	
	$img = new Imagick();
	$img->readImageFile($handle);
	$img->resizeImage(500, 500, 0, 0);
	$img->writeImage('test.jpg');
	
	header( "Content-Type: image/jpeg" );
	//echo $img;

	echo "still running \n";

	fclose($handle);
?>