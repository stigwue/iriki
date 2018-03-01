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
    			'properties' => array(
    				'string' => array(
    					'description' => 'A string property',
    					'type' => 'string',
    					'unique' => true
    				),
    				'number' => array(
    					'description' => 'A numeric property',
    					'type' => 'number'
    				)
    			),
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
					'url_parameters' => array('string'),
					'exempt' => array(),
					'authenticate' => true
					//'group_authenticate' =>
					//'user_authrnticate' =>
					//http_method => GET, POST, *PUT etc
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

    
	/**
	 * @depends test_Model
     * @depends test_Model_Status
     * @depends test_RouteDef
     */
    public function test_getActionDetails_no_desc($model, $model_status, $route_def)
    {
    	unset($route_def['model_name']['action_name']['description']);

    	$model_status = \iriki\model::getActionDetails($model, $model_status, $route_def);

    	$this->assertEquals("", $model_status['action_details']['description']);
    }
	/**
	 * @depends test_Model
     * @depends test_Model_Status
     * @depends test_RouteDef
     */
    public function test_getActionDetails_desc($model, $model_status, $route_def)
    {
    	$desc = $route_def['model_name']['action_name']['description'];

    	$model_status = \iriki\model::getActionDetails($model, $model_status, $route_def);

    	$this->assertEquals($desc, $model_status['action_details']['description']);
    }

    /**
	 * @depends test_Model
     * @depends test_Model_Status
     * @depends test_RouteDef
     */
    public function test_getActionDetails_no_params($model, $model_status, $route_def)
    {
    	unset($route_def['model_name']['action_name']['parameters']);

    	$model_status = \iriki\model::getActionDetails($model, $model_status, $route_def);

    	$this->assertEquals(array(), $model_status['action_details']['parameters']);
    }
	/**
	 * @depends test_Model
     * @depends test_Model_Status
     * @depends test_RouteDef
     */
    public function test_getActionDetails_params($model, $model_status, $route_def)
    {
    	$params = $route_def['model_name']['action_name']['parameters'];

    	$model_status = \iriki\model::getActionDetails($model, $model_status, $route_def);

    	$this->assertEquals($params, $model_status['action_details']['parameters']);
    }

    /**
	 * @depends test_Model
     * @depends test_Model_Status
     * @depends test_RouteDef
     */
    public function test_getActionDetails_no_url_params($model, $model_status, $route_def)
    {
    	unset($route_def['model_name']['action_name']['url_parameters']);

    	$model_status = \iriki\model::getActionDetails($model, $model_status, $route_def);

    	$this->assertEquals(array(), $model_status['action_details']['url_parameters']);
    }
	/**
	 * @depends test_Model
     * @depends test_Model_Status
     * @depends test_RouteDef
     */
    public function test_getActionDetails_url_params($model, $model_status, $route_def)
    {
    	$url_params = $route_def['model_name']['action_name']['url_parameters'];

    	$model_status = \iriki\model::getActionDetails($model, $model_status, $route_def);

    	$this->assertEquals($url_params, $model_status['action_details']['url_parameters']);
    }
}

?>
