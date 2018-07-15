<?php

class collectionItemTest extends \PHPUnit\Framework\TestCase
{
	public function test_class_exist()
    {
    	$status = class_exists('\kronos\collection_item');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
	 * @depends test_class_exist
     */
	public function test_add_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection_item',
                'action' => 'add',
                'url_parameters' => array(),
                'params' => array(
            		'collection_id' => '5b4119a60f6d2fe4640041be',
                    'type' => 'noun',
                    'model' => '5aec74b2363ab8180c50ee90'
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
     * @depends test_class_exist
     */
    public function test_add_fail($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection_item',
                'action' => 'add',
                'url_parameters' => array(),
                'params' => array(
                    'collection_id' => '5b4119a60f6d2fe4640041be',
                    'type' => 'noun',
                    'model' => '5aec74b2363ab8180c50ee90'
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

        $this->assertEquals(400, $status['code']);
    }

    /**
	 * @depends test_create_success
     */
    public function test_remove_success($id)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection_item',
                'action' => 'remove',
                'url_parameters' => array(),
                'params' => array(
            		'collection_id' => '5b4119a60f6d2fe4640041be',
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

	/**
     * @depends test_create_success
     */
	public function test_remove_by_model_success()
	{
		$request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection',
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

}

?>
