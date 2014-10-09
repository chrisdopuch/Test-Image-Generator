<?php

	// function to apply a filter to an image magick image
	// input: IMagick handle $img, string $filter 
	function filter( $img, $filter ) {


		switch ($filter) {

			case "none":
				//print "no filter applied\n";
				break;

			case "poisson":
			    //print "add poisson noise\n";
				$img->addNoiseImage ( imagick::NOISE_POISSON   );
				break;

			case "laplacian":
			    //print "add laplacian noise\n";
				$img->addNoiseImage ( imagick::NOISE_LAPLACIAN    );
				break;

			case "blurHigh":
				//print "add blur high \n";
				$img->blurImage( 50, 10 );
				break;

			case "blurMid":
				//print "add blur mid \n";
				$img->blurImage( 25, 5 );
				break;

			case "blurLow":
				//print "add blur low \n";
				$img->blurImage( 5, 3 );
				break;

			case "blurRadialHigh":
				//print "add high radial blur\n";
				$img->radialBlurImage(2);
				break;

			case "blurRadialLow":
				//print "add low radial blur\n";
				$img->radialBlurImage(1);
				break;

			case "darken":
				//print "add darken\n";
				$img->gammaImage(0.4);
				break;

			case "brighten":
				//print "add brighten\n";
				$img->gammaImage(1.5);
				break;
		}

		return $img;


	}

	/*

	if( $argc < 2 ) { 
		print "Error: expects command line argument <filter>\n";
		return;
	}


	$file_name = "backgrounds/field1.jpg";
	$output_file_name = "test_output.jpg";
	$base = new Imagick($file_name);

	// do filtering
	$base = filter($base, $argv[1]);

	$base->writeImage($output_file_name);

	*/

?>