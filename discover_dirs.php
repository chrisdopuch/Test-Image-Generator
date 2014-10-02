<?php

	function discover_dirs($directory) {

		// check that it's a directory
		if(!is_dir($directory)) {
			// not a directory, so return NULL
			return NULL;
		}

		// scan the directory
		$array = scandir($directory);
		// remove the . and  ..
		$array = array_slice($array, 2);
		return $array;

	};

?>