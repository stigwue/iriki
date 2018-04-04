<?php

namespace iriki_tests;

class userTest extends \PHPUnit\Framework\TestCase
{
	public function test_create_success()
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'signup',
                'url_parameters' => array(),
                'params' => array(
                    'username' => 'root',
                    'hash' => 'p455w0rd'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request
        );

        $this->assertEquals(200, $status['code']);
    }   
}

?>