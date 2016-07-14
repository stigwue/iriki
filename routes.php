<?php

	$routes = array(
		//pulse specific
		'pulse' => [
			'action' => array(
				'about' => null,
				'api' => null,
				'test' => [/*any or all models*/]
			)
		]
		//general for all models
		'*' => [
			'action' => array(
				'add' => [],
				'read' => ['id'],
				'edit' => ['id', []],
				'delete' => ['id']
			)
		],
		//for other models
		//'user' => 
	);

?>