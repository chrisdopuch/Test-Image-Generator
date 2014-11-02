<?php

	require "parse_config.php";
	require "write_json.php";

	$THRESHOLD = 100;
	$OUTPUT = "report.json";

	$options = getopt("f:l:");
	$first = $options["f"];
	$last = $options["l"];

	$key = parse_config($first);
	$answers = parse_config($last);

	// this will contain all the output data for the grading, and will be saved to file as JSON
	$report = array();

	//print "========== VAR DUMP KEY ==========\n";
	//var_dump($key);
	//print "========== VAR DUMP ANSWERS ==========\n";
	//var_dump($answers);

	if( count($key) != count($answers) ) {
		print "Error: files do not match in array length\n";
		exit();
	}

	print "Count of key: " . count($key) . "\n";

	// for each image
	for( $j = 0; $j < count($key); $j++ ) {

		print "Next image: " . $j . "\n";

		// new assoc array to store all data for this image
		// gets pushed onto report when finished
		$image = array();

		$key_object = $key[$j];
		$answer_object = $answers[$j];
		$false_pos = 0;
		$missed = 0;
		$num_found = 0;



		foreach ($key_object as $key => $value) {

			print "Next key: " . $key . "\n";

			$key_property = $key_object[$key];
			$answer_property = $answer_object[$key];

			switch($key) {

				case "targetSetSizes": 
					$image["number of targets"] = $answer_property;
					break;

				case "filters":
					//just log the name of the filter
					$image["filter"] = $key_property;
					break;

				case "targets":	

					if( count($key_property) > 0 && count($answer_property) < 0 ) {	
						usort($key_property, "cmp");
						usort($answer_property, "cmp");

						//get the top item in each list
						$key_target = array_slice($key_property, 0); 
						$answer_target = array_slice($answer_property, 0); 
					}

					while( count($key_property) > 0 && count($answer_property) < 0 ) {
						// check if they are closer enough to be the same target
						if ( abs( $key_target["x"] - $answer_target["x"] ) > $THRESHOLD ) {
							//X is not close, so pop the smaller one off the list
							if( $key_target["x"] < $answer_target["x"] ) {
								array_pop($key_property);
								$key_target = array_slice($key_property, 0); 
								// 1.) missed target
								$missed++;
							} else {
								array_pop($answer_property);
								$answer_target = array_slice($answer_property, 0); 
								// 4.) false positive
								$false_pos++;
							}
						} else {
							// compare the Y coordinates
							if ( abs( $key_target["y"] - $answer_target["y"] ) > $THRESHOLD ) {
								

								// the Y coords are not close, so check additional items until both X and Y coords are far off
								$i = 1;
								if ( count( $answer_property ) < 2 ) {

									$next = array_slice($answer_property, $i);

									while ( abs( $key_target["x"] - $next["x"] ) <= $THRESHOLD ) {
										if ( abs( $key_target["y"] - $next["y"] ) > $THRESHOLD ) {
											$i++;
											$next = array_slice($answer_property, $i);
										} else {

											$found = true;
											$num_found += 1;
											
											// do the grading of the two targets

											// pop off the top of the key target array
											array_pop($key_property);
											$key_target = array_slice($key_property, 0); 
											// splice out the answer target
											array_splice($answer_property, $i, 1);

										}

									}

									// 3.) TODO: on exit of while, if no match found, then missed anothe target
									if( !$found ) {
										$missed++;
									}

								} else {
									// 2.) handle there are no other answers, so missed a key target
									$missed++;
								}
								
							} else {
								// X and Y close, do the grading for that target pair

							}

							if( $key_target["x"] < $answer_target["x"] ) {
								array_pop($key_property);
								$key_target = array_slice($key_property, 0); 
							} else {
								array_pop($answer_property);
								$answer_target = array_slice($answer_property, 0); 
							}
						}
					}

					// check the remaining items in the non empty list and add to either misses or false pos
					if ( count($key_property) > 0 ) {
						$missed += count($key_property);
					} else if ( count($answer_property) > 0 ) {
						$false_pos += count($answer_property);
					}

					// calculate the aggregate stats such as 
					// num targets found
					// num false pos
					// num missed
					$image["found targets"] = $num_found;
					$image["false positives"] = $false_pos;
					$image["missed targets"] = $missed;

					break;


			}

		}

		// we have gone through each attribute, so $image is done, push onto $report
		array_push($report, $image);

	}

	print "Finished main loop \n";

	// log the report to JSON file
	write_json($report, $OUTPUT);

	function cmp($a, $b) {
	    if ($a["x"] == $b["x"]) {
	        return 0;
	    }
	    return ($a["x"] < $b["x"]) ? -1 : 1;
	}

?>