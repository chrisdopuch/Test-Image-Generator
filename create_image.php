<?php



	error_reporting(-1);

	require "get_positions.php";

	$file_name = "backgrounds/field1.jpg";
	$output_file_name = "test_output.jpg";
	$target_dir = "targets/*.png";

	$num_targets = 5;
	$targets_used = 0;
	
function create_image($input) {

	try {

		$base = new Imagick($input["backgroundsDir"] . "/" . $input["background"]);

		// get all the target images
		$targets = $input["targets_set"];

		$targets_array = array();
		
		var_dump($targets);

		foreach($targets as $target) {
			$targets_used++;
			array_push($targets_array, array( "file_name" => ""));
			if($targets_used >= $num_targets)
				break;
		}

		$targets_array = get_positions($base, 200, $targets_array);

		var_dump($targets_array);
		
		//Initialize targets array index.
		$input['targets'] = array();

		
		foreach($targets_array as $target){
		
			//Load image
			$not_rotated = imagecreatefrompng($target['file_name']);
			
			//Initialize alpha
			$pngTransparency = imagecolorallocatealpha($not_rotated , 0, 0, 0, 127);
			
			//Rotate image
			$rotated = imagerotate($not_rotated, $target['angle'], $pngTransparency);
			
			//Save the alpha channel
			imagesavealpha($rotated, true);
			
			//Save image
			imagepng($rotated, "tmp_rotated.png");
		
			$target_img = new Imagick('tmp_rotated.png'); 
			
			//$target_img->rotateImage(new ImagickPixel('none'), $target['angle']); // first arg makes it transparent 

			// composite target on top of base
			$base->compositeImage($target_img, Imagick::COMPOSITE_OVER, $target['x'], $target['y']);
			
			//Counter for number of targets used.
			//$count = $count++;
			
			$info = parse_target_name($target["file_name"]);
			$info["x"] =  $target['x'];
			$info["y"] =  $target['y'];
			$info["angle"] = $target["angle"];
			
			array_push($input['targets'], $info);

		}

		$base->writeImage($output_file_name);
		

	} catch (Exception $e) {
		echo "Caught exception: " . $e->getMessage() . "\n";
		$base->clear();
		$targets->clear();
	}
	
	$base->clear();
	$targets->clear();
	
	
	return($input);
}

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