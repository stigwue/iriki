<?php

namespace iriki_tests;

class authTest extends \PHPUnit\Framework\TestCase
{
    public function test_class_exist()
    {
        $status = class_exists('\iriki\auth');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
     * @depends test_class_exist
     */
	public function test_initiate_success($status)
    {
        $details = array(
            'key_type' => 'long',
            'ttl' => IRIKI_SESSION_SHORT + (1 * 24 * 60 * 60),
            'status' => true,
            'tag' => 'some_tag',
            'user_id' => '5ae6bb830f6d2fea120041b6'
        );

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'initiate',
                'url_parameters' => array(),
                'params' => $details
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(200, $status['code']);

        $details['key'] = $status['data'];
        return $details;
    }

    /**
     * @depends test_class_exist
     */
    public function test_initiate_using_key_success($status)
    {
        $details = array(
            'key' => 'speak_friend_and_enter',
            'ttl' => IRIKI_SESSION_SHORT + (1 * 24 * 60 * 60),
            'status' => true,
            'tag' => 'some_other_tag',
            'user_id' => '5ae6bb830f6d2fea120041b6'
        );

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'initiate_using_key',
                'url_parameters' => array(),
                'params' => $details
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(200, $status['code']);

        $details['key'] = $status['data'];
        return $details;
    }

    /**
     * @depends test_class_exist
     */
    public function test_initiate_open_success($status)
    {
        $details = array(
            'key_type' => 'short',
            'ttl' => IRIKI_SESSION_SHORT + (1 * 24 * 60 * 60),
            'tag' => 'some_other_other_tag',
            'user_id' => '5ae6bb830f6d2fea120041b6'
        );

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'initiate_open',
                'url_parameters' => array(),
                'params' => $details
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(200, $status['code']);

        $details['key'] = $status['data'];
        return $details;
    }

    /**
     * @depends test_initiate_open_success
     */
    public function test_update_open_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'update_open',
                'url_parameters' => array(),
                'params' => array(
                    'key' => $details['key'],
                    'status' => true,
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(200, $status['code']);
    }

    /**
     * @depends test_initiate_success
     */
    public function test_get_token_success($details)
    {
        //should return a valid token as revoke not yet called
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'get_token',
                'url_parameters' => array(),
                'params' => array(
                    'key' => $details['key'],
                    'remember' => false
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            (count($status['data']) == 1)
        );
    }

    /**
     * @depends test_initiate_success
     */
    public function txst_revoke_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'revoke',
                'url_parameters' => array(),
                'params' => array(
                    'key' => $details['key']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(200, $status['code']);
    }

    //get_token, should be invalid
    /**
     * @depends test_initiate_success
     */
    public function test_get_token_fail($details)
    {
        //should return a valid token as revoke not yet called
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'get_token',
                'url_parameters' => array(),
                'params' => array(
                    'key' => $details['key'],
                    'remember' => true
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(true,
            ($status['code'] == 400)
        );
    }

    /**
     * @depends test_initiate_success
     */
    public function test_extend_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'extend',
                'url_parameters' => array(),
                'params' => array(
                    'key' => $details['key'],
                    'ttl_extend_by' => 5 * 60 //5 minutes
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(200, $status['code']);
    }

    /**
     * @depends test_initiate_success
     */
    public function test_read_by_key_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'read_by_key',
                'url_parameters' => array(),
                'params' => array(
                    'key' => $details['key']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            (count($status['data']) == 1)
        );
    }

    /**
     * @depends test_initiate_success
     */
    public function txst_read_by_user_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'read_by_user',
                'url_parameters' => array(),
                'params' => array(
                    'user_id' => $details['user_id']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            (count($status['data']) == 1)
        );
    }

    /**
     * @depends test_initiate_success
     */
    public function test_read_by_tag_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'auth',
                'action' => 'read_by_tag',
                'url_parameters' => array(),
                'params' => array(
                    'tag' => $details['tag']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            (count($status['data']) == 1)
        );
    }
}

?>