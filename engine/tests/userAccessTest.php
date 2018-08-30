<?php

namespace iriki_tests;

class userAccessTest extends \PHPUnit\Framework\TestCase
{
	public function test_create_success()
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user_access',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'user_id' => '5b66012b0f6d2f82890041ba',
                    'user_group_id' => '5b66012b0f6d2f82890041bc'
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
}

?>