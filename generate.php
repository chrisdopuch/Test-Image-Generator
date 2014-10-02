<?php

	// This is the main file which will generate the test images

	require 'parseConfig.php';
	require 'discoverDirs.php';

	$CONFIG_FILE = "config.txt";

	$config = parse_config($CONFIG_FILE);

	$config["targets"] = discover_dirs($config["targetsDir"]);
	$config["backgrounds"] = discover_dirs($config["backgroundsDir"]);

?>
