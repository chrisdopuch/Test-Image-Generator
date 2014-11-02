<?php
function grade_target($target1, $target2) {

$grade = array();

if( $target1["letter"] == $target2["letter"] ) {
	array_push($grade, "Letter Match.");
}

if( $target1["letter_color"] == $target2["letter_color"] ) {
	array_push($grade, "Letter Color Match.");
}

if( $target1["shape"] == $target2["shape"] ) {
	array_push($grade, "Shape Match.");
}

if( $target1["shape_color"] == $target2["shape_color"] ) {
	array_push($grade, "Shape Color Match.");
}

$x1 = $target1["x"];
$x2 = $target2["x"];

$y1 = $target1["y"];
$y2 = $target2["y"]:

$b = pow(($x1 - $x2), 2);
$c = pow(($y1 - $y2), 2);

$distance = sqrt($b + $c);

array_push($grade, "Distance between two images is: " . $distance);

return($grade);
}
?>