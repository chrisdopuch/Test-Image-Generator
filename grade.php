<?php

	require "parse_config.php";
	require "write_json.php";
	require "is_match.php";
	require "grading_comparison.php";

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

	// print "Count of key: " . count($key) . "\n";

	// for each image
	for( $j = 0; $j < count($key); $j++ ) {

		print "Next image: " . $j . "\n";

		// new assoc array to store all data for this image
		// gets pushed onto report when finished
		$image = array();

		$key_object = $key[$j];
		$answer_object = $answers[$j];
		$false_pos = 0;
		$misses = 0;
		$num_found = 0;



		foreach ($key_object as $property => $value) {

			// print "Next key: " . $property . "\n";

			$key_property = $key_object[$property];
			$answer_property = $answer_object[$property];

			switch($property) {

				case "targetSetSizes": 
					$image["number of targets"] = $answer_property;
					break;

				case "filters":
					//just log the name of the filter
					$image["filter"] = $key_property;
					break;

				case "targets":	
					
					// check if there are any targets
					if( count($key_property) > 0 && count($answer_property) > 0 ) {

						// iterate through each key target and determine if an answer matches 
						// store all matches to use later for grading actual answers
						$matches = array();
						$match_index = 0;

						for ( $key_index = 0; $key_index < count($key_property); $key_index++ ) {

							$matched = false;

							for ( $answer_index = 0; $answer_index < count($answer_property); $answer_index++ ) {

								if ( is_match( $key_property[$key_index], $answer_property[$answer_index], $THRESHOLD ) ) {

									// match found, add to the matches array
									$matched = true;
									$num_found++;

									$match = array();
									$match["key_target"] = $key_property[$key_index];
									$match["answer_target"] = $answer_property[$answer_index];
									$matches[$match_index++] = $match;

								} 

							}

							if ( !$matched ) 
								$misses++;

						}

						// execute grading logic on each of the matched targets and add the grade info to the matches array
						for ( $i = 0; $i < count($matches); $i++ ) {

							$grade = grade_target( $matches[$i]["key_target"], $matches[$i]["answer_target"] );

							$this_match = $matches[$i];

							$matches[$i] = array_merge( $this_match, $grade );

						}

						// record the accuracy parameters for this image
						$false_pos = count($answer_property) - count($matches);
						$image["false positives"] = $false_pos;
						$num_found = count($matches);
						$image["found targets"] = $num_found;
						$image["missed targets"] = $misses;
						$image["matched targets"] = $matches;

						//var_dump($matches);
						print "Number of targets: " . $image["number of targets"] . "\n";
						print "Found targets: " . $image["found targets"] . "\n";
						print "False positives: " . $image["false positives"] . "\n";
						print "Missed targets: " . $image["missed targets"] . "\n";
						print "\n\n";


					} else {
						// handle case of no target keys or answers
						$image["found targets"] = 0;
						$image["false positives"] = 0;
						$image["missed targets"] = 0;

						print "No targets, moving on\n";
						print "Count of key_property: " . count($key_property) . "\n";
						print "Count of answer_property: " . count($answer_property) . "\n";
					}


					/* if( count($key_property) > 0 && count($answer_property) > 0 ) {	
						usort($key_property, "cmp");
						usort($answer_property, "cmp");

						//get the top item in each list
						$key_target = array_slice($key_property, 0)[0]; 
						$answer_target = array_slice($answer_property, 0)[0]; 
					} else {
						print "No targets, moving on\n";
						print "Count of key_property: " . count($key_property) . "\n";
						print "Count of answer_property: " . count($answer_property) . "\n";
					}

					while( count($key_property) > 0 && count($answer_property) > 0 ) {

						//var_dump($key_target);
						//var_dump($answer_target);

						// check if they are closer enough touch(filename) be the same target
						if ( abs( $key_target["x"] - $answer_target["x"] ) > $THRESHOLD ) {
							//X is not close, so pop the smaller one off the list
							if( $key_target["x"] < $answer_target["x"] ) {
								array_pop($key_property);
								$key_target = array_slice($key_property, 0)[0]; 
								// 1.) missed target
								$missed++;
							} else {
								array_pop($answer_property);
								$answer_target = array_slice($answer_property, 0)[0]; 
								// 4.) false positive
								$false_pos++;
							}
						} else {
							// compare the Y coordinates
							if ( abs( $key_target["y"] - $answer_target["y"] ) > $THRESHOLD ) {
								

								// the Y coords are not close, so check additional items until both X and Y coords are far off
								$i = 1;
								if ( count( $answer_property ) < 2 ) {

									$next = array_slice($answer_property, $i)[0];

									while ( abs( $key_target["x"] - $next["x"] ) <= $THRESHOLD ) {
										if ( abs( $key_target["y"] - $next["y"] ) > $THRESHOLD ) {
											$i++;
											$next = array_slice($answer_property, $i)[0];
										} else {

											$found = true;
											$num_found += 1;
											
											// do the grading of the two targets

											// pop off the top of the property target array
											array_pop($key_property);
											$key_target = array_slice($key_property, 0)[0]; 
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

								print "Match found\n";

								// X and Y close, do the grading for that target pair
								$num_found++;
								array_pop($key_property);
								$key_target = array_slice($key_property, 0)[0];
								array_pop($answer_property);
								$answer_target = array_slice($answer_property, 0)[0];
							}

							if( $key_target["x"] < $answer_target["x"] ) {
								array_pop($key_property);
								$key_target = array_slice($key_property, 0)[0]; 
							} else {
								array_pop($answer_property);
								$answer_target = array_slice($answer_property, 0)[0]; 
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
					print $image["number of targets"] . "\n";
					$image["found targets"] = $num_found;
					print $image["found targets"] . "\n";
					$image["false positives"] = $false_pos;
					print $image["false positives"] . "\n";
					$image["missed targets"] = $missed;
					print $image["missed targets"] . "\n";

					// var_dump($image);

					// print "finished targets\n";

					*/

					break;



			} 

		}

		// we have gone through each attribute, so $image is done, push onto $report
		array_push($report, $image);

	}

	// print "Finished main loop \n";

	// log the report to JSON file
	write_json($report, $OUTPUT);

	function cmp($a, $b) {
	    if ($a["x"] == $b["x"]) {
	        return 0;
	    }
	    return ($a["x"] < $b["x"]) ? -1 : 1;
	}

?>