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
	if( $target2["shape"] == "square" || $target2["shape"] == "rhombus" || $target2["shape"] == "parallelogram" || $target2["shape"] == "rectangle" || $target2["shape"] == "trapezoid" || $target2["shape"] == "kite") {
		$grade['shape accuracy'] = 1;
	} else {
		$grade['shape accuracy'] = 0;
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

print "Letter character accuracy: " . $grade['letter character accuracy'] . "\n";
print "Letter color accuracy: " . $grade['letter color accuracy'] . "\n";
print "Shape accuracy: " . $grade['shape accuracy'] . "\n";
print "Shape color accuracy: " . $grade['shape color accuracy'] . "\n";
print "Distance from key target: " . $grade['distance from key'] . "\n\n";

return($grade);
}
?>