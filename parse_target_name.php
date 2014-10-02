<?php

	function parse_target_name($name){

		// strip out the file extension
		$path_parts = pathinfo($name);
		$stripped_name = $path_parts['filename'];

		// make the name into an array with delim of "_"
		$delim = "_";
		$array = explode($delim, $stripped_name);

		$components = array(

			'letter' => $array[0],
			'letter_color' => $array[1],
			'shape' => $array[2],
			'shape_color' => $array[3]

		);

		return $components;

	}

?>