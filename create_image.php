<?php



	error_reporting(-1);

	require "get_positions.php";
	require "parse_target_name.php";
	require "filter.php";
	
	
function create_image($input, $num) {

	$output_dir = "output/";

	print "_________ VAR DUMP INPUT __________\n";
	var_dump($input);

	try {

		$targets_used = 0;
		$num_targets = $input["targetSetSizes"];

		$base = new Imagick($input["backgroundsDir"] . "/" . $input["background"]);

		// get all the target images
		$targets = $input["targets_set"];

		$targets_array = array();
		
		print "___________________VAR DUMP TARGETS __________\n";
		var_dump($targets);

		for ( $i = 0; $i < $num_targets; $i++ ) {
			//$targets_used++;
			//if($targets_used >= $num_targets)
			//	break;
			array_push($targets_array, array( "file_name" => $targets[rand(0, count($targets))]));
			
		}

		print "_________ VAR DUMP TARGETS ARRAY __________\n";
		var_dump($targets_array);

		$targets_array = get_positions($base, 100, $targets_array);

		print "_________ VAR DUMP TARGETS ARRAY POST GET POSITIONS __________\n";
		var_dump($targets_array);
		
		//Initialize targets array index.
		$input['targets'] = array();

		

		if( !empty($targets_array[0]['file_name']) ) {
			foreach($targets_array as $target){

				var_dump($target["file_name"]);
			
				//Load image
				$not_rotated = imagecreatefrompng($input["targetsDir"] . "/" . $target["file_name"]);
				
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

				$target_img->clear();

			}
		}

		$base = filter( $base, $input['filters'] );

		$base->writeImage($output_dir . $num . ".jpg");
		

	} catch (Exception $e) {
		echo "Caught exception: " . $e->getMessage() . "\n";
		$base->clear();
		$target_img->clear();
		//$targets->clear();
	}
	
	$base->clear();
	//$targets->clear();
	
	
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