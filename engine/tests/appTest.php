<?php

namespace iriki_tests;

class appTest extends \PHPUnit\Framework\TestCase
{
    public function test_class_exist()
    {
        $status = class_exists('\iriki\app');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_failed($status)
    {
        $title = 'Administrator';

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'app',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'kronos',
                    'description' => 'The chronological chronicler.',
                    //'path' => '/some/where' //the missing parameter
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

        $this->assertEquals(400, $status['code']);
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
                'model' => 'app',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'kronos',
                    'description' => 'The chronological chronicler.',
                    'path' => '/some/where'
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
    }

}

?>