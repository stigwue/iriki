<?php
	
	//main model is the pulse itself
	$models = array(
		'pulse' => array(
			'properties' => [
				'user', 'authentication', 'session', 'setting',

				/*insert your own models here*/
			],

			'relationships' => array(
				'belongsto' => [],
				'hasmany' => ['user']
			)
		)
	);

	//child models
	$user = array(
		'properties' => [
			//every models first property is an id element
			'id' => ['unique'],
			'username' => ['unique', 'email'],
			'group' => ['number']
		],
		'relationships' => [
			'belongsto' => ['pulse'],
			'hasmany' => ['authentication', 'session', 'setting']

		]
	);

	$models['user'] => $user;
?>