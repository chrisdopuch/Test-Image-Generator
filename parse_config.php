<?php
function parse_config($configFile) {
	//Get contents from config file passed in.
	$jsonFromFile = file_get_contents($configFile);

	//Array to hold key values brought in from config file.
	$contentsDecoded = json_decode($jsonFromFile, true);
	
	//Return array.
	return($contentsDecoded);
}
?>