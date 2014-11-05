<?php

	/*
	 * Function to evaluate if a target is within a certain threshold
	 */

	function is_match( $t1, $t2, $threshold ) {

		$x_diff = abs( $t1["x"] - $t2["x"] );
		$y_diff = abs( $t1["y"] - $t2["y"] );
		return ( $x_diff <= $threshold && $y_diff <= $threshold );

	}

	/*

	$threshold = 50;

	$target1 = array();
	$target1["x"] = 0;
	$target1["y"] = 0;

	$target2 = array();
	$target2["x"] = 100;
	$target2["y"] = 0;

	$target3 = array();
	$target3["x"] = 0;
	$target3["y"] = 100;

	$target4 = array();
	$target4["x"] = 100;
	$target4["y"] = 100;

	$target5 = array();
	$target5["x"] = 25;
	$target5["y"] = 25;

	$target6 = array();
	$target6["x"] = 50;
	$target6["y"] = 50;

	print "Test 1: correct answer is true\n";
	print is_match( $target1, $target1, $threshold ) . "\n";

	print "Test 2: correct answer is true\n";
	print is_match( $target2, $target2, $threshold ) . "\n";

	print "Test 3: correct answer is true\n";
	print is_match( $target3, $target3, $threshold ) . "\n";

	print "Test 4: correct answer is true\n";
	print is_match( $target4, $target4, $threshold ) . "\n";

	print "Test 5: correct answer is true\n";
	print is_match( $target1, $target5, $threshold ) . "\n";

	print "Test 6: correct answer is false\n";
	print is_match( $target1, $target2, $threshold ) . "\n";

	print "Test 7: correct answer is false\n";
	print is_match( $target1, $target3, $threshold ) . "\n";

	print "Test 8: correct answer is false\n";
	print is_match( $target1, $target4, $threshold ) . "\n";

	print "Test 9: correct answer is false\n";
	print is_match( $target2, $target4, $threshold ) . "\n";

	print "Test 10: correct answer is false\n";
	print is_match( $target3, $target4, $threshold ) . "\n";

	print "Test 11: correct answer is false\n";
	print is_match( $target2, $target3, $threshold ) . "\n";

	print "Test 12: correct answer is true\n";
	print is_match( $target1, $target6, $threshold ) . "\n";

	*/

?>