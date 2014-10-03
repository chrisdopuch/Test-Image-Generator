<?php



	error_reporting(-1);

	require "get_positions.php";

	$file_name = "backgrounds/field1.jpg";
	$output_file_name = "test_output.jpg";
	$target_dir = "targets/*.png";

	$num_targets = 5;
	$targets_used = 0;

	

	try {

		$base = new Imagick($file_name);

		// get all the target images
		$targets = new Imagick(glob($target_dir));

		$targets_array = array();

		foreach($targets as $target) {
			$targets_used++;
			array_push($targets_array, array( "file_name" => $target->getImageFilename()));
			if($targets_used >= $num_targets)
				break;
		}

		$targets_array = get_positions($base, 200, $targets_array);

		var_dump($targets_array);

		
		foreach($targets_array as $target){
			$target_img = new Imagick($target['file_name']);
			//$target_img->rotateImage(new ImagickPixel('#FFFFFF'), $target['angle']); // first arg makes it transparent
			// composite target on top of base
			$base->compositeImage($target_img, Imagick::COMPOSITE_DEFAULT, $target['x'], $target['y']);
		}

		$base->writeImage($output_file_name);

	} catch (Exception $e) {
		echo "Caught exception: " . $e->getMessage() . "\n";
		$base->clear();
		$targets->clear();
	}
	
	$base->clear();
	$targets->clear();


	/*//Background image.
	$handle = fopen('koala.jpg', 'rb');
	
	$base = new Imagick();
	$base->readImageFile($handle);
	$new_base = $base->clone();
	$new_base->resizeImage(50,50,Imagick::FILTER_LANCZOS,1);
	$new_base->writeImage('small_koala.jpg');
	$new_base->clear();
	$new_base->destroy();
	$base->clear();
	$base->destroy();
	
	header( "Content-Type: image/jpeg" );
	echo $base;

	echo "still running \n";

	//fclose($handle);*/
?>