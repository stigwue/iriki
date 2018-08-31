<?php

namespace iriki_tests;

class userAccessTest extends \PHPUnit\Framework\TestCase
{
    public function test_create_user_success()
    {
        $details = array(
            'username' => 'user',
            'hash' => 'password'
        );

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => $details
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(200, $status['code']);

        $details['user_id'] = $status['data'];

        return $details;
    }

    /**
     * @depends test_create_user_success
     */
    public function test_create_user_group_success($details)
    {
        $details['user_group'] = 'user group';

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_group',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'title' => $details['user_group']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );


        $this->assertEquals(200, $status['code']);

        $details['user_group_id'] = $status['data'];

        return $details;
    }


	/**
     * @depends test_create_user_group_success
     */
    public function test_create_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_access',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'user_id' => $details['user_id'],
                    'user_group_id' => $details['user_group_id']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(200, $status['code']);

        $details['user_access_id'] = $status['data'];
    }

    public function test_create_alias_success()
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'alias',
                'action' => 'group_add',
                'url_parameters' => array(),
                'params' => array(
                    'user_id' => '5b66012b0f6d2f82890041bb',
                    'user_group_id' => '5b66012b0f6d2f82890041bd'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(200, $status['code']);
    }


    public function test_user_in_group_success()
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_access',
                'action' => 'user_in_group',
                'url_parameters' => array(),
                'params' => array(
                    'user_id' => '5b66012b0f6d2f82890041bb',
                    'user_group_id' => '5b66012b0f6d2f82890041bd'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            ($status['message'] == true)
        );
    }

    /**
     * @depends test_create_user_group_success
     */
    public function test_user_in_group_title_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_access',
                'action' => 'user_in_group_title',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $details['username'],
                    'title' => $details['user_group']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            ($status['message'] == true)
        );
    }

    /**
     * @depends test_create_user_group_success
     */
    public function test_user_in_any_group_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_access',
                'action' => 'user_in_any_group',
                'url_parameters' => array(),
                'params' => array(
                    'user_id' => $details['user_id'],
                    'user_group_id_array' => [
                        $details['user_group_id']
                    ]
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            ($status['message'] == true)
        );
    }

    /**
     * @depends test_create_user_group_success
     */
    public function test_user_in_any_group_title_success($details)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_access',
                'action' => 'user_in_any_group_title',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $details['username'],
                    'title_array' => [
                        $details['user_group']
                    ]
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode, ignore authentication
        );

        $this->assertEquals(true,
            ($status['code'] == 200) AND
            ($status['message'] == true)
        );
    }
}

?>