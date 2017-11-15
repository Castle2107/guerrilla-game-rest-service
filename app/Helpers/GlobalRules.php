<?php

namespace App\Helpers;

class GlobalRules {
	
	const ASSAULT_UNIT = array(
		'money' => 20,
		'people' => 1,
		'oil' => 25,
		'points' => 3,
		'name' => 'assault'
	);

	const ENGINEER_UNIT = array(
		'money' => 10,
		'people' => 1,
		'oil' => 25,
		'points' => 3,
		'name' => 'engineer'
	);

	const TANK_UNIT = array(
		'money' => 200,
		'people' => 8,
		'oil' => 500,
		'points' => 17,
		'name' => 'tank'
	);

	const BUNKER_UNIT = array(
		'money' => 300,
		'people' => 8,
		'oil' => 200,
		'points' => 20,
		'name' => 'bunker'
	);

	/*
	 * Loot variables
	 */
	const ASSAULT_LOOT 	= 25;
	const ENGINEER_LOOT = 60;
	const TANK_LOOT 	= 200;
	const BUNKER_LOOT 	= 0;

	/*
	 * Damage Inflicted variables
	 */
	const ASSAULT_DAMAGE_INFLICTED = array(
		'offense'  => 80,
		'defense'  => 20,
		'assault'  => 0.5,
		'engineer' => 0.3,
		'tank'	   => 5,
		'bunker'   => 5
	);

	const ENGINEER_DAMAGE_INFLICTED = array(
		'offense'  => 30,
		'defense'  => 70,
		'assault'  => 0.8,
		'engineer' => 0.5,
		'tank'	   => 5,
		'bunker'   => 5
	);

	const TANK_DAMAGE_INFLICTED = array(
		'offense'  => 500,
		'defense'  => 20,
		'assault'  => 0.1,
		'engineer' => 0.9,
		'tank'	   => 0.6,
		'bunker'   => 0.9
	);

	const BUNKER_DAMAGE_INFLICTED = array(
		'offense'  => 0,
		'defense'  => 600,
		'assault'  => 0.05,
		'engineer' => 0.9,
		'tank'	   => 1,
		'bunker'   => 0
	);

}