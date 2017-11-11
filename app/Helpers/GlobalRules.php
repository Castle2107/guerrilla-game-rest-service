<?php

namespace App\Helpers;

class GlobalRules {
	
	const ASSAULT_UNIT = array(
		'money' => 20,
		'people' => 1,
		'oil' => 25,
		'points' => 3
	);

	const ENGINEER_UNIT = array(
		'money' => 10,
		'people' => 1,
		'oil' => 25,
		'points' => 3
	);

	const TANK_UNIT = array(
		'money' => 200,
		'people' => 8,
		'oil' => 500,
		'points' => 17
	);

	const BUNKER_UNIT = array(
		'money' => 300,
		'people' => 8,
		'oil' => 200,
		'points' => 20
	);


}