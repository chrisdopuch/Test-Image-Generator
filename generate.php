<?php

	// This is the main file which will generate the test images

	require 'parse_config.php';
	require 'discover_dirs.php';
	require 'write_json.php';

	$CONFIG_FILE = "config.json";
	$OUTPUT_FILE = "output.json";

	$config = parse_config($CONFIG_FILE);

	$config["targets"] = discover_dirs($config["targetsDir"]);
	$config["backgrounds"] = discover_dirs($config["backgroundsDir"]);
	print($config["backgroundsDir"]);

	// loop through the combinations of options and call the image generation for each combination
	

?>
