<?php

	/*function combine($curr, $remaining) {

		if(empty($remaining))
			return;

		// if curr is empty, push a new empty array onto it
		if(empty($curr))
			array_push($curr, array());

		$next = array_pop($remaining);

		foreach($curr as $curr_key => $curr_value) {
			echo "curr key: " . $curr_key . " and curr value: " . $curr_value . "\n";
			foreach ($next as $next_key => $next_value) {
				echo "next key: " . $next_key . " and next value: " . $next_value . "\n";
				if(this_key == 0) {
					array_push($curr_value, $next_value);
				} else {
					// clone the current row
					$new_row = $curr_value;
					array_push($new_row, $next_value);
					array_push($curr, $new_row);
				}
				
			}
			$curr = combine($curr, $remaining);
		}

		return $curr;

	}

	$test_array = array(

		'sizes' => array(1, 2, 3),
		'backgrounds' => array('one', 'two', 'three'),
		'filters' => array('none', 'blur')//,
		//'single' => 999

	);

	$result = array();

	$result = combine($result, $test_array);

	var_dump($result);*/

	function combos($data, $all = array(), $group = array(), $val = null, $i = 0)
	{
		if (isset($val))
		{
			array_push($group, $val);
		}
	 
		if ($i >= count($data))
		{
			array_push($all, $group);
		}
		else
		{
			foreach ($data as $v)
			{
				combos($data, &$all, $group, $v, $i + 1);
			}
		}
	 
		return $all;
	}
	 
	$data = array
	(
		array('a', 'b'),
		array('e', 'f', 'g'),
		array('w', 'x', 'y', 'z'),
	);

	$test_array = array(

		'sizes' => array(1, 2, 3),
		'backgrounds' => array('one', 'two', 'three'),
		'filters' => array('none', 'blur')//,
		//'single' => 999

	);
	 
	//$combos1 = combos($data);
	$combos2 = combos($test_array);
	 
	//print_r($combos1);
	var_dump($combos2);


?>