<?php

	function write_json($array, $file) {

		if(!is_array($array))
			return;

		$json = json_encode($array);

		$file_handle = fopen($file, 'w'); 

		fwrite($file_handle, $json);

		fclose($file_handle);

	}

?>