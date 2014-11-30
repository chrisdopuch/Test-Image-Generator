<?php

	require "parse_config.php";
	require "write_json.php";
	require "is_match.php";
	require "grading_comparison.php";

	$THRESHOLD = 300;
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


			switch($property) {

				case "targetSetSizes": 
					$image["number of targets"] = $key_property;
					break;

				case "filters":
					//just log the name of the filter
					$image["filter"] = $key_property;
					break;

				case "targets":	

					$answer_property = $answer_object[$property];
					
					// check if there are any targets
					if( count($key_property) > 0 || count($answer_property) > 0 ) {

						// iterate through each key target and determine if an answer matches 
						// store all matches to use later for grading actual answers
						$matches = array();
						$match_index = 0;

						for ( $key_index = 0; $key_index < count($key_property); $key_index++ ) {

							//print "outer loop " . $key_index . " of key target array\n";

							$matched = false;

							for ( $answer_index = 0; $answer_index < count($answer_property); $answer_index++ ) {

								//print "inner loop " . $answer_index . " of answer target array\n";

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

						// check if there were no key targets but there are false positives


						// execute grading logic on each of the matched targets and add the grade info to the matches array
						for ( $i = 0; $i < count($matches); $i++ ) {

							$grade = grade_target( $matches[$i]["key_target"], $matches[$i]["answer_target"] );

							$this_match = $matches[$i];

							$matches[$i] = array_merge( $this_match, $grade );

						}

						// record the accuracy parameters for this image
						$image["number of targets"] = count($key_property);
						$false_pos = count($answer_property) - count($matches);
						$image["false positives"] = $false_pos;
						$num_found = count($matches);
						$image["found targets"] = $num_found;
						$image["missed targets"] = $misses;
						$image["matched targets"] = $matches;

						//var_dump($matches);
						


					} else {
						// handle case of no target keys or answers
						$image["found targets"] = 0;
						$image["false positives"] = 0;
						$image["missed targets"] = 0;

						

						//print "No targets, moving on\n";
						//print "Count of key_property: " . count($key_property) . "\n";
						//print "Count of answer_property: " . count($answer_property) . "\n";
					}


					

					break;



			} 

		}

		print "Number of targets: " . $image["number of targets"] . "\n";
		print "Found targets: " . $image["found targets"] . "\n";
		print "False positives: " . $image["false positives"] . "\n";
		print "Missed targets: " . $image["missed targets"] . "\n\n";
		print "\n\n";

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