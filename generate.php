<?php

	// This is the main file which will generate the test images

	require 'parse_config.php';
	require 'discover_dirs.php';
	require 'write_json.php';
	require 'combine.php';
	require 'create_image.php';

	$CONFIG_FILE = "config.json";
	$OUTPUT_FILE = "output.json";

	$config = parse_config($CONFIG_FILE);

	$config["targets_set"] = array(discover_dirs($config["targetsDir"][0]));
	$config["background"] = discover_dirs($config["backgroundsDir"][0]); // make this an array because we don't actually need all combinations of target images
	//var_dump($config);

	$combinations = combine($config);
	$output_array = array();

	//var_dump($combinations);

	// iterate through combinations and call the create_image function with that combination of params
	for( $i = 0; $i < count($combinations); $i++ ) {
		// push its output onto the output array
		try {
			array_push( $output_array, create_image($combinations[$i], $i));
		} catch (Exception $e) {
			echo $e->errorMessage();
		}
	}

	// save the output array to file as JSON data
	write_json($output_array, $OUTPUT_FILE);

	print count($combinations);
	

?>
