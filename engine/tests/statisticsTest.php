<?php

namespace iriki_tests;

class statisticsTest extends \PHPUnit\Framework\TestCase
{
    public function test_class_exist()
    {
        $status = class_exists('\iriki\statistics');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_success($status)
    {
        $details = array(
            'code' => "stat_code",
            'timestamp' => 'dd/mm/yyyy',
            'label' => 'Age|Degree',
            'value' => '20,1'
        );

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'statistics',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => $details
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
        $details['_id'] = $id;
        return $details;
    }

    /**
     * @depends test_create_success
     */
    public function test_read_by_code_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'statistics',
                'action' => 'read_by_code',
                'url_parameters' => array(),
                'params' => array(
                    'code' => $details['code']
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

        $this->assertEquals(true, count($status['data']) == 1);
    }

    /**
     * @depends test_create_success
     */
    public function test_read_by_code_range_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'statistics',
                'action' => 'read_by_code_range',
                'url_parameters' => array(),
                'params' => array(
                    'code' => $details['code'],
                    'from_timestamp' => 0,
                    'to_timestamp' => time() + 1
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

        $this->assertEquals(true, count($status['data']) == 1);
    }

    /**
     * @depends test_create_success
     */
    public function test_read_by_code_delta_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'statistics',
                'action' => 'read_by_code_delta',
                'url_parameters' => array(),
                'params' => array(
                    'code' => $details['code']
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

        $this->assertEquals(true, (
                count($status['data']) == 1 AND
                $status['data'][0]['value'] == '0,0'
            )
        );
    }

    /**
     * @depends test_create_success
     */
    public function test_read_by_code_delta_range_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'statistics',
                'action' => 'read_by_code_delta_range',
                'url_parameters' => array(),
                'params' => array(
                    'code' => $details['code'],
                    'from_timestamp' => 0,
                    'to_timestamp' => time() + 1
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

        $this->assertEquals(true, (
                count($status['data']) == 1 AND
                $status['data'][0]['value'] == '0,0'
            )
        );
    }

    /**
     * @depends test_create_success
     */
    public function test_delete_by_code_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'statistics',
                'action' => 'delete_by_code',
                'url_parameters' => array(),
                'params' => array(
                    'code' => $details['code']
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
            ($status['code'] == 200 AND
            $status['message'] == true)
        );
    }
}

?>