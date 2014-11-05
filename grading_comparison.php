<?php
function grade_target($target1, $target2) {

$grade = array();

if( $target1["letter"] == $target2["letter"] ) {
	$grade['letter character accuracy'] = 1;
} else {
	$grade['letter character accuracy'] = 0;
}

if( $target1["letter_color"] == $target2["letter_color"] ) {
	$grade['letter color accuracy'] = 1;
} else {
	$grade['letter color accuracy'] = 0;
}

//First target passed in is answer, second target is key.
if( $target1["shape"] == "4-gon") {
	//Handles various naming for four-sided shapes.
	if( $target2["shape"] == "square" || "rhombus" || "parallelogram" || "rectangle" || "trapezoid" || "kite") {
		$grade['shape accuracy'] = 1;
	}
} else if ( $target1["shape"] == $target2["shape"] ) {
	$grade['shape accuracy'] = 1;
} else {
	$grade['shape accuracy'] = 0;
}

if( $target1["shape_color"] == $target2["shape_color"] ) {
	$grade['shape color accuracy'] = 1;
} else {
	$grade['shape color accuracy'] = 0;
}

//Calculate distance between two targets.
$x1 = $target1["x"];
$x2 = $target2["x"];

$y1 = $target1["y"];
$y2 = $target2["y"];

$b = pow(($x1 - $x2), 2);
$c = pow(($y1 - $y2), 2);

$distance = sqrt($b + $c);

$grade["distance from key"] = $distance;

return($grade);
}
?>