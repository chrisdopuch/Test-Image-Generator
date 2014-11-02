<?php

	require parse_config.php;

	$THRESHOLD = 100;

	$options = getopt("f:l:");
	$first = $options["f"];
	$last = $options["l"];

	$key = parse_config($first);
	$answers = parse_config($last);

	if( count($key) != count($answers) ) {
		print "Error: files do not match in array length\n";
		exit();
	}

	// for each image
	for ( $i = 0; $i < count($key); $i++ ) {

		$key_object = $key[$i];
		$answer_object = $answers[$i];

		$false_pos = 0;
		$missed = 0;

		foreach ($key_object as $key => $value) {

			$key_property = $key_object["$key"];
			$answer_property = $answer_object["key"];

			switch($key) {


				case "targetSetSizes": 
					if ( $key_property == $answer_property ) {
						// log correct
					} else {
						//log incorrect
					}
					break;

				case "filters":
					//just log the name of the filter
					break;

				case "targets":
					// if targets is empty, ignore it
					if( count($targets) <= 0 ) {
						//skip it
					} else {
						usort($key_property, "cmp");
						usort($answer_property, "cmp");

						//get the top item in each list
						$key_target = array_slice($key_property, 0); 
						$answer_target = array_slice($answer_property, 0); 

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

						// calculate the aggregate stats such as 
						// num targets found
						// num false pos
						// num missed
						// 
					}

					break;


			}

		}

	}


	function cmp($a, $b) {
	    if ($a["x"] == $b["x"]) {
	        return 0;
	    }
	    return ($a["x"] < $b["x"]) ? -1 : 1;
	}

?>