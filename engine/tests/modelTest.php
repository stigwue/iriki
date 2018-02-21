<?php

class modelTest extends PHPUnit_Framework_TestCase
{
	public function test_Model()
    {
    	$model = 'model_name';

        $this->assertTrue(true, 'Test model specified.');

    	return $model;
    }

	public function test_RouteAction()
    {
    	$action = 'action_name';

        $this->assertTrue(true, 'Test action specified.');

    	return $action;
    }

	public function test_ModelDef()
    {
    	$model_def = array(
    		'model_name' => array(
    			'description' => 'The model description.',
    			'properties' => array(),
    			'relationships' => array(
    				'belongsto' => array(),
    				'hasmany' => array()
    			)
    		)
    	);

        $this->assertTrue(true, 'Test model built.');

    	return $model_def;
    }

	public function test_RouteDef()
    {
    	$route_def = array(
    		'model_name' => array(
    			'action_name' => array(
    				'description' => 'The route action description.',
					'parameters' => array(),
					'url_parameters' => array(),
					'exempt' => array(),
					'authenticate' => true
    			)
    		)
    	);

        $this->assertTrue(true, 'Test route built.');

    	return $route_def;
    }

    public function test_RouteDefaults()
    {
    	$route_default = array(
		    "create" => [
				"description" => "Add a new instance of the model to storage.",
				"parameters" => [],
				"exempt" => ["_id"]
			],
			"read" => [
				"description" => "Returns the properties of the matching model.",
				"parameters" => ["_id"],
				"url_parameters" => ["_id"]
			],
			"read_all" => [
				"description" => "Returns the properties of the matching model.",
				"parameters" => [],
				"exempt" => ["*"]
			],
			"update" => [
				"description" => "Updates a model instance by supplying new properties and the id of the model to update.",
				"parameters" => []
			],
			"delete" => [
				"description" => "Deletes a model instance by supplying an id of the mode.",
				"parameters" => ["_id"],
				"url_parameters" => ["_id"]
			]
    	);

        $this->assertTrue(true, 'Test default routes built.');

    	return $route_default;
    }

	//model_status, prefilled by route::matchUrl
	/**
	 * @depends test_Model
	 * @depends test_RouteAction
     * @depends test_ModelDef
     * @depends test_RouteDefaults
     */
    public function test_Model_Status($model, $action, $model_def, $route_default)
	{
		$model_status = array(
            'str' => $model, //string, model
            'str_full' => '\\namespace\\model_name', //string, full model including namespace
            'defined' => true, //boolean, model defined in app or engine config
            'exists' => false, //boolean, model class exists
            'details' => $model_def['model_name'], //array, model description, properties and relationships
            'app_defined' => true, //boolean, model defined in app. otherwise engine
            'action'=> $action, //string, action
            'default' => $route_default, //array, default actions
            'action_defined' => false, //boolean, action defined
            'action_default' => false, //boolean, action is default defined
            'action_exists' => true, //boolean, action exists in class
            'action_details' => null //array, action description, parameters, exempt, authenticate
        );

        $this->assertTrue(true, 'Test model_status built.');

        return $model_status;
	}
}

?>
