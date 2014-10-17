<?php

	// Function to generate a number of target positions and rotations
	// Expected input: the Imagick handle of the base image, minimum distance between targets, and an assoc. array with the following structure:
	//		$array {
	//			[0] => array {
	//						"file_name" => "targets/some_target_file.png"
	//					}
	//			[1] => array {
	//						"file_name" => "targets/some_other_target_file.png"
	//					}
	//			...
	//			[n] = > array {
	//						"file_name" => "targets/the_last_target.png"
	//					}
	//		}
	// The arrays at each index of the original input array will have additional key:value pairs added according to the following format:
	//			array {
	//				"file_name" => "targets/some_target_file.png",
	//				"x" => some integer value,
	//				"y" => some integer value,
	//				"angle" => some double value
	//			}
	// Other functions will in turn add more data to these arrays (such as the parse_target_name() function)

	function get_positions($img, $min_dist, $array) {

		if(empty($array)) {
			print "Hey man, $array param to get_positions is empty!\n";
			return NULL;
		}

		// first get the dimensions of the Imagick object
		$dimensions = $img->getImageGeometry();

		$locations = array(); // array to store locations for checking the min dist
		$failures = 0; // count the number of too close failures.
		$epic_fail = false; // start over flag
		$max_failures = 10;

		$width = $dimensions['width'];
		$height = $dimensions['height'];

		do {
			for($i = 0; $i < count($array); $i++) {



				if(empty($array[$i])){ 
					print "Error: $target array in get_positions is empty!\n";
					return NULL;
				}

				// generate the position of the target
				$x = rand(0, ($width - $min_dist));
				$y = rand(0, ($height - $min_dist));

				$too_close = false;

				do {
					// check that it isn't too close to the other locations
					foreach($locations as $location) {
						if(abs($location['x'] - $x) <= $min_dist || abs($location['y'] - $y) <= $min_dist) {
							// too close, regenerate
							$too_close = true;
							$failures += 1;
							$x = rand(0, $width - $min_dist);
							$y = rand(0, $height - $min_dist);
							print "Too close \n";
							print "Fails: " . $failures . "\n";
							break;
						}
						$too_close = false;
					}

					$epic_fail = false;

					if( $failures > $max_failures) {
						// too many fails, we are stuck, so just start over
						$epic_fail = true;
						$locations = array(); // reset locations
						$failures = 0;
						print "EPIC FAIL: Too many fails\n";
						var_dump($array);
						print $img->getImageFilename();
						break 2; // break out to the outermost loop
					}

				} while ($too_close);

				// if not too close, then add the location to the $target
				$array[$i]['x'] = $x;
				$array[$i]['y'] = $y;
				$array[$i]['angle'] = rand(0, 360); // generate the angle
				// also add it to locations
				array_push($locations, array('x' => $x, 'y' => $y));

			}
			$failures = 0; // reset fails
		} while ($epic_fail);

		return $array;

	}

	/* test cases

	$array = array();

	$array[0] = array();
	$array[0]['file_name'] = "test_file_name.png";
	$array[1] = array();
	$array[1]['file_name'] = "test_file_name.png";
	$array[2] = array();
	$array[2]['file_name'] = "test_file_name.png";
	$array[3] = array();
	$array[3]['file_name'] = "test_file_name.png";
	$array[4] = array();
	$array[4]['file_name'] = "test_file_name.png";
	$array[5] = array();
	$array[5]['file_name'] = "test_file_name.png";
	$array[6] = array();
	$array[6]['file_name'] = "test_file_name.png";
	$array[7] = array();
	$array[7]['file_name'] = "test_file_name.png";
	$array[8] = array();
	$array[8]['file_name'] = "test_file_name.png";
	$array[9] = array();
	$array[9]['file_name'] = "test_file_name.png";
	$array[10] = array();
	$array[10]['file_name'] = "test_file_name.png";

	$img = new Imagick("test_image.jpg");
	$min_dist = 100; //arbitrary

	var_dump(get_positions($img, $min_dist, $array));

	*/

?>