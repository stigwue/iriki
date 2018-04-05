<?php

class groupTest extends \PHPUnit\Framework\TestCase
{
	public function test_class_exist()
    {
    	$status = class_exists('\iriki\user_group');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
	 * @depends test_class_exist
     */
	public function test_create_success($status)
    {
        $title = 'Administrator';

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'group',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
            		'title' => $title
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
            true //enable test mode
        );

        $this->assertEquals(200, $status['code']);

        $id = $status['data'];
        return $title;
    }

    /**
	 * @depends test_create_success
     */
    public function test_read_by_title_success($title)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'group',
                'action' => 'read_by_title',
                'url_parameters' => array(),
                'params' => array(
            		'title' => $title
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
            true //enable test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            count($status['data']) == 1) AND
            ($status['data'][0]['title'] == $title)
        );

	}

    /*
    public function test_delete_success($id)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'group',
                'action' => 'delete',
                'url_parameters' => array(),
                'params' => array(
            		'title' => $id
                )
            )
        );

        $model_profile = iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
            true //enable test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            ($status['message'] == true))
        );
	}
    */
}

?>
