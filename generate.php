<?php

	// This is the main file which will generate the test images

	require 'parseConfig.php';
	require 'discoverDirs.php';

	$CONFIGFILE = "config.txt";

	$config = parseConfig($CONFIGFILE);

	$config["targets"] = discoverTargets($config["targetsDir"]);
	$config["backgrounds"] = discoverBackgrounds($config["backgroundsDir"]);

?>
