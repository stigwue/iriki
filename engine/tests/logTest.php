<?php

namespace iriki_tests;

class logTest extends \PHPUnit\Framework\TestCase
{
    public function test_class_exist()
    {
        $status = class_exists('\iriki\log');

        $this->assertEquals(true, $status);

        return $status;
    }

	public function test_request()
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'log',
                'action' => 'request',
                'url_parameters' => array(),
                'params' => array(
                    'user' => 'root',
                    'model' => 'log',
                    'action' => 'request',
                    'timestamp' => time(NULL),
                    'parent' => '-',
                    'tag' => 'test'
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

        $id = $status['data'];
        return $id;
    } 

    

    /**
     * @depends test_request
     */
    public function test_response($id)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'log',
                'action' => 'response',
                'url_parameters' => array(),
                'params' => array(
                    'user' => 'root',
                    'model' => 'log',
                    'action' => 'request',
                    'timestamp' => time(NULL),
                    'parent' => $id,
                    'tag' => '200'
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