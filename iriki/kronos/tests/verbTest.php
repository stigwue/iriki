<?php

class verbTest extends \PHPUnit\Framework\TestCase
{
	public function test_class_exist()
    {
    	$status = class_exists('\kronos\verb');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
	 * @depends test_class_exist
     */
	public function test_create_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'verb',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
            		'name' => 'A verb'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

    	$this->assertEquals(200, $status['code']);

        $id = $status['data'];
        return $id;
    }

    /**
	 * @depends test_create_success
     */
    public function test_read_success($id)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'verb',
                'action' => 'read',
                'url_parameters' => array(),
                'params' => array(
            		'_id' => $id
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            count($status['data']) == 1) AND
            ($status['data'][0]['_id'] == $id)
        );

	}


	//read all
	public function test_read_all_success()
	{
		$request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'verb',
                'action' => 'read_all',
                'url_parameters' => array(),
                'params' => array(
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            count($status['data']) == 1)
        );
	}

    /**
	 * @depends test_create_success
     */
    public function test_update_success($id)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'verb',
                'action' => 'update',
                'url_parameters' => array(),
                'params' => array(
            		'_id' => $id,
            		'name' => 'Another verb'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            ($status['message'] == true))
        );
	}

    /**
	 * @depends test_create_success
     */
    public function txst_delete_success($id)
	{
		$request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'verb',
                'action' => 'delete',
                'url_parameters' => array(),
                'params' => array(
            		'_id' => $id
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            ($status['message'] == true))
        );

	}

}

?>
