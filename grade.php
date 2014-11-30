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

	// open two files to write for CSV, one for targets, one for images
	$targets_fp = fopen('targets.csv', 'w');
	$images_fp = fopen('images.csv', 'w');

	// print the headers for the CSV files

	// images.csv will contain the metrics for the each entire image
	fputcsv($images_fp, array(

		"image",
		"background",
		"filter",
		"targets_count", // target set size
		"found_targets",
		"missed_targets",
		"false_positives"

	));

	// targets.csv will contain the matched targets, the missed targets, and the false positives
	fputcsv($targets_fp, array(

		"image",
		"background",
		"filter",
		"status", // either 'matched', 'missed', or 'false positive'
		"letter_color_accuracy",
		"letter_character_accuracy",
		"shape_accuracy",
		"shape_color_accuracy",
		"distance",
		"key_letter_color",
		"key_letter_character",
		"key_shape",
		"key_shape_color",
		"key_x",
		"key_y",
		"answer_letter_color",
		"answer_letter_character",
		"answer_shape",
		"answer_shape_color",
		"answer_x",
		"answer_y",
		"target_angle"

	));

	// for each image
	for( $j = 0; $j < count($key); $j++ ) {

		$image_csv = array();

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

						//keep track of which answer targets are matches (we will iterate through non-matches and log them to the CSV file as false positives)
						$answer_matches = array();

						for ( $answer_index = 0; $answer_index < count($answer_property); $answer_index++ ) {
							$answer_matches[$answer_index] = false;
						}

						for ( $key_index = 0; $key_index < count($key_property); $key_index++ ) {

							//print "outer loop " . $key_index . " of key target array\n";

							$matched = false;

							for ( $answer_index = 0; $answer_index < count($answer_property); $answer_index++ ) {

								//print "inner loop " . $answer_index . " of answer target array\n";

								if ( is_match( $key_property[$key_index], $answer_property[$answer_index], $THRESHOLD ) ) {

									// match found, add to the matches array
									$matched = true;
									$answer_matches[$answer_index] = true;
									$num_found++;

									$match = array();
									$match["key_target"] = $key_property[$key_index];
									$match["answer_target"] = $answer_property[$answer_index];
									$matches[$match_index++] = $match;

								} 

							}

							if ( !$matched ) {

								$misses++;

								// log the missed target to the CSV file
								$target_csv = array();
								$target_csv[0] = $j;
								$target_csv[1] = $key_object['background'];
								$target_csv[2] = $key_object['filters'];
								$target_csv[3] = 'missed';
								$target_csv[4] = NULL;
								$target_csv[5] = NULL;
								$target_csv[6] = NULL;
								$target_csv[7] = NULL;
								$target_csv[8] = NULL;
								$target_csv[9] = $key_property[$key_index]['letter_color'];
								$target_csv[10] = $key_property[$key_index]['letter'];
								$target_csv[11] = $key_property[$key_index]['shape'];
								$target_csv[12] = $key_property[$key_index]['shape_color'];
								$target_csv[13] = $key_property[$key_index]['x'];
								$target_csv[14] = $key_property[$key_index]['y'];
								$target_csv[15] = NULL;
								$target_csv[16] = NULL;
								$target_csv[17] = NULL;
								$target_csv[18] = NULL;
								$target_csv[19] = NULL;
								$target_csv[20] = NULL;
								$target_csv[21] = $key_property[$key_index]['angle'];


								// write the target row to the target.csv
								fputcsv($targets_fp, $target_csv);

							}

						}

						// loop through the answer targets, and for each unmatched target, add a row to the CSV as a false positive
						//keep track of which answer targets are matches (we will iterate through non-matches and log them to the CSV file as false positives)
						for ( $answer_index = 0; $answer_index < count($answer_property); $answer_index++ ) {

							if ( $answer_matches[$answer_index] == false ) {

								// false positive, let's log it
								$target_csv = array();
								$target_csv[0] = $j;
								$target_csv[1] = $key_object['background'];
								$target_csv[2] = $key_object['filters'];
								$target_csv[3] = 'false positive';
								$target_csv[4] = NULL;
								$target_csv[5] = NULL;
								$target_csv[6] = NULL;
								$target_csv[7] = NULL;
								$target_csv[8] = NULL;
								$target_csv[9] = NULL;
								$target_csv[10] = NULL;
								$target_csv[11] = NULL;
								$target_csv[12] = NULL;
								$target_csv[13] = NULL;
								$target_csv[14] = NULL;
								$target_csv[15] = $answer_property[$answer_index]['letter_color'];
								$target_csv[16] = $answer_property[$answer_index]['letter'];
								$target_csv[17] = $answer_property[$answer_index]['shape'];
								$target_csv[18] = $answer_property[$answer_index]['shape_color'];
								$target_csv[19] = $answer_property[$answer_index]['x'];
								$target_csv[20] = $answer_property[$answer_index]['y'];
								$target_csv[21] = NULL;


								// write the target row to the target.csv
								fputcsv($targets_fp, $target_csv);

							}

						}


						// execute grading logic on each of the matched targets and add the grade info to the matches array
						for ( $i = 0; $i < count($matches); $i++ ) {

							$grade = grade_target( $matches[$i]["key_target"], $matches[$i]["answer_target"] );

							$this_match = $matches[$i];

							$matches[$i] = array_merge( $this_match, $grade );

							// write matched target to the CSV
							$target_csv = array();
							$target_csv[0] = $j;
							$target_csv[1] = $key_object['background'];
							$target_csv[2] = $key_object['filters'];
							$target_csv[3] = 'matched';
							$target_csv[4] = $grade['letter color accuracy'];
							$target_csv[5] = $grade['letter character accuracy'];
							$target_csv[6] = $grade['shape accuracy'];
							$target_csv[7] = $grade['shape color accuracy'];
							$target_csv[8] = $grade['distance from key'];
							$target_csv[9] = $matches[$i]['key_target']['letter_color'];
							$target_csv[10] = $matches[$i]['key_target']['letter'];
							$target_csv[11] = $matches[$i]['key_target']['shape'];
							$target_csv[12] = $matches[$i]['key_target']['shape_color'];
							$target_csv[13] = $matches[$i]['key_target']['x'];
							$target_csv[14] = $matches[$i]['key_target']['y'];
							$target_csv[15] = $matches[$i]['answer_target']['letter_color'];
							$target_csv[16] = $matches[$i]['answer_target']['letter'];
							$target_csv[17] = $matches[$i]['answer_target']['shape'];
							$target_csv[18] = $matches[$i]['answer_target']['shape_color'];
							$target_csv[19] = $matches[$i]['answer_target']['x'];
							$target_csv[20] = $matches[$i]['answer_target']['y'];
							$target_csv[21] = $matches[$i]['key_target']['angle'];

							// write the target row to the target.csv
							fputcsv($targets_fp, $target_csv);

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

		$image_csv[0] = $j;
		$image_csv[1] = $key_object['background'];
		$image_csv[2] = $key_object['filters'];
		$image_csv[3] = $key_object['targetSetSizes'];
		$image_csv[4] = $image['found targets'];
		$image_csv[5] = $image['missed targets'];
		$image_csv[6] = $image['false positives'];
		
		// write the image row to the image.csv
		fputcsv($images_fp, $image_csv);
	}

	// print "Finished main loop \n";

	// log the report to JSON file
	write_json($report, $OUTPUT);

	fclose($images_fp);
	fclose($targets_fp);

	function cmp($a, $b) {
	    if ($a["x"] == $b["x"]) {
	        return 0;
	    }
	    return ($a["x"] < $b["x"]) ? -1 : 1;
	}

?>