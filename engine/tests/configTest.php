<?php

namespace iriki_tests;

class configTest extends \PHPUnit\Framework\TestCase
{
    public function test_class_exist()
    {
        $status = class_exists('\iriki\config');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
     * @depends test_class_exist
     */
	public function test_create_success()
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'config',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'key' => 'foo',
                    'value' => 'bar'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        return $status['data'];
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
                'model' => 'config',
                'action' => 'read',
                'url_parameters' => array(),
                'params' => array(
                    '_id' => $id
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

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

    /**
     * @depends test_create_success
     */
    public function test_read_by_key_success($id)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'config',
                'action' => 'read_by_key',
                'url_parameters' => array(),
                'params' => array(
                    'key' => 'foo'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

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

    /**
     * @depends test_create_success
     */
    public function test_read_all_success($id)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'config',
                'action' => 'read_all',
                'url_parameters' => array(),
                'params' => array()
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

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
}

?>