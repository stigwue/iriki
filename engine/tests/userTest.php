<?php

namespace iriki_tests;

class userTest extends \PHPUnit\Framework\TestCase
{
    public function test_class_exist()
    {
        $status = class_exists('\iriki\user');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
     * @depends test_class_exist
     */
	public function test_signup_success($status)
    {
        $user = array(
            'username' => 'root',
            'hash' => 'p455w0rd'
        );

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'signup',
                'url_parameters' => array(),
                'params' => $user
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true
        );

        $user['_id'] = $status['data'];

        $this->assertEquals(200, $status['code']);

        return $user;
    }

    /**
     * @depends test_signup_success
     */
    public function test_read_success($user)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'read',
                'url_parameters' => array(),
                'params' => array(
                    '_id' => $user['_id']
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
            (
                $status['code'] == 200 AND
                count($status['data']) == 1
            )
        );
    }

    /**
     * @depends test_signup_success
     */
    public function test_read_by_username_success($user)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'read_by_username',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $user['username']
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
            (
                $status['code'] == 200 AND
                count($status['data']) == 1
            )
        );
    } 

    /**
     * @depends test_signup_success
     */
    public function test_reset_auth_success($user)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'reset_auth',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $user['username']
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
            (
                $status['code'] == 200
            )
        );

        $password = $status['message'];
        return $password;
    }

    /**
     * @depends test_signup_success
     * @depends test_reset_auth_success
     */
    public function test_authenticate_success($user, $password)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'authenticate',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $user['username'],
                    'hash' => $password,
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
            (
                $status['code'] == 200 AND
                $status['message'] == true AND
                isset($status['data'])
            )
        );

        $token = $status['data'];
        return $token;
    }

    /**
     * @depends test_authenticate_success
     * @depends test_signup_success
     * @depends test_reset_auth_success
     */
    public function test_change_auth_success($token, $user, $password)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'change_auth',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $user['username'],
                    'hash_old' => $password,
                    'hash_new' => $user['hash']
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
            (
                $status['code'] == 200 AND
                $status['message'] == true
            )
        );
    }



    /**
     * @depends test_signup_success
     */
    public function test_update_auth_success($user)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'update_auth',
                'url_parameters' => array(),
                'params' => array(
                    '_id' => $user['_id'],
                    'hash' => $user['hash']
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
            (
                $status['code'] == 200 AND
                $status['message'] == true
            )
        );
    }

    /**
     * @depends test_signup_success
     */
    public function test_delete_by_username_success($user)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'delete_by_username',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $user['username']
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
            (
                $status['code'] == 200 AND
                $status['message'] == true
            )
        );
    } 
}

?>