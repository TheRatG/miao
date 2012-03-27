<?php
return array(
	'TestConfig' => array(
		'firstParam' => 1,
		'secondParam' => 2,
		'View' => array(
			'firstViewParam' => 'view_param_1',
			'Article' => array( 'articleParam' => 'articleValue' ) ),
		'thirdParam' => 3,
		'PropertyFirst' => array( '__construct' => array( 'a', 'b' ) ),
		'PropertySecond' => array(
			'__construct' => array( 'test', array( 'a', array( 'b' ) ) ) ) ),
	'skipedParam' => 'skParam' );