<?php

	function combine( $remaining ) {

		// if there are no remaining 
		if ( empty( $remaining ) ) {
			//print "No items in remaining array \n";
			return array();
		}

		// if there are remaining values
		// pop the top key and value off of the $remaining array
		$value = reset( $remaining );
		$key = key( $remaining );
		unset($remaining[$key]);

		// call combine() again and operate on it's output
		$result_array = combine( $remaining );

			if( empty( $result_array ) ) {
				//print "Result array was empty, add new items to it \n";
				// if the result array was empty, then push a new assoc array for each item in $value 
				for ( $i = 0; $i < count($value); $i++ ) {
					array_push( $result_array, array(
							$key => $value[$i]
						) );
				}
			} else {
				//print "Call to combine with result array not empty\n";
				// otherwise, iterate from the bottom of $result_array up to the top
				for ( $i = (count( $result_array ) - 1); $i >= 0; $i-- ) {
					//$handle = fopen ("php://stdin","r");
					//$line = fgets($handle);
					//print "Index " . $i . " of result array\n";
					// get the row at that index and add the first value to it
					$row = $result_array[$i];
					$row[$key] = $value[0];
					// then reinsert it back into the array at the original index
					$result_array[$i] = $row;
					// for the remaining items in $value, add them to row and then push those onto the array
					for ( $j = 1; $j < count($value); $j++ ) {
						$row[$key] = $value[$j];
						array_push( $result_array, $row );
					}
				}
			}

			return $result_array;

	}

	/*

	$data = array
	(
		"key1" => array('a', 'b'),
		"key2" => array('e', 'f', 'g')
	);

	$test_array = array(
		'sizes' => array(1, 2, 3),
		'backgrounds' => array('one', 'two', 'three'),
		'filters' => array('none', 'blur'),
		'single' => array( 999 )
	);
	 
	//$combos1 = combos($data);
	$combos2 = combine($test_array);
	 
	//print_r($combos1);
	var_dump($combos2);

	*/


?>