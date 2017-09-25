<?php

require_once(__DIR__ . '/../../iriki/request.php');

class mongodbsuccessTest extends \PHPUnit\Framework\TestCase
{
    //please note that this test creates a valid internal db handle
    //so tests that rely on this handle being invalid will fail
    //so order tests accordingly, will you?
    public function test_doInitialise_success()
    {
    	iriki\engine\mongodb::doDestroy();

        $status = iriki\engine\mongodb::doInitialise(
            array(
                'type' => '\\iriki\\engine\\mongodb',
                'server' => 'mongodb://localhost:27017',
                'db' => 'kronos'
            )
        );

        //assert
        $this->assertEquals(true, $status);
    }

    public function test_doDestroy_success()
    {
    	$status = iriki\engine\mongodb::doDestroy();

        //assert
        $this->assertEquals(true, $status);
    }

	public function test_doCreate_success()
    {
        iriki\engine\mongodb::doDestroy();
    	
        iriki\engine\mongodb::doInitialise(
            array(
                'type' => '\iriki\engine\mongodb',
                'server' => 'mongodb://localhost:27017',
                'db' => 'kronos'
            )
        );

        $request = new \iriki\request();
        //db_type
        $request->setDBType('\iriki\engine\mongodb');
        //model status
        $request->setModelStatus(
            array(
                'str' => 'test', //string, model
                'str_full' => '\iriki\test', //string, full model including namespace
                'defined' => true, //boolean, model defined in app or engine config
                'exists' => true, //boolean, model class exists
                'details' => array(
                    "description" => "A test model.",
                    "properties" => array(
                        "_id" => array(
                            "description" => "Internal unique ID.",
                            "type" => "key",
                            "unique" => true
                        ),
                        "property" => array(
                            "description" => "Some other property.",
                            "type" => "string"
                        )
                    ),
                    "relationships" => array(
                        "belongsto" => ["test2"],
                        "hasmany" => []
                    )
                ), //array, model description, properties and relationships
                'app_defined' => false, //boolean, model defined in app. otherwise engine
                'action'=> 'action', //string, action
                'default' => array(), //array, default actions
                'action_defined' => true, //boolean, action defined
                'action_default' => false, //boolean, action is default defined
                'action_exists' => true, //boolean, action exists in class
                'action_details' => array(
                    "action" => array(
                        "description" => "Test action",
                        "parameters" => ["description"],
                        "authenticate" => false
                    )
                ) //array, action description, parameters, exempt, authenticate
            )
        );
        //parameter_status
        $request->setParameterStatus(
            array(
                //properties supplied
                'final' => array('property'),
                //missing properties that should have been supplied
                'missing' => array(),
                //extra properties that should not have been supplied
                'extra' => array(),
                //these, especially for mongodb have to be saved as mongoids
                'ids' => array()
            )
        );
        //data
        $request->setData(
            array(
                'property' => "property's value"
            )
        );
        //meta
        //?
        //session
        //$request->setSession('user_session_token');

        $status = \iriki\engine\mongodb::doCreate($request);

        //assert
        $this->assertEquals(true, $status['message']);
    }

    public function test_doRead_success()
    {
        iriki\engine\mongodb::doDestroy();
    	
        iriki\engine\mongodb::doInitialise(
            array(
                'type' => '\iriki\engine\mongodb',
                'server' => 'mongodb://localhost:27017',
                'db' => 'kronos'
            )
        );

        $request = new \iriki\request();
        //db_type
        $request->setDBType('\iriki\engine\mongodb');
        //model status
        $request->setModelStatus(
            array(
                'str' => 'test', //string, model
                'str_full' => '\iriki\test', //string, full model including namespace
                'defined' => true, //boolean, model defined in app or engine config
                'exists' => true, //boolean, model class exists
                'details' => array(
                    "description" => "A test model.",
                    "properties" => array(
                        "_id" => array(
                            "description" => "Internal unique ID.",
                            "type" => "key",
                            "unique" => true
                        ),
                        "property" => array(
                            "description" => "Some other property.",
                            "type" => "string"
                        )
                    ),
                    "relationships" => array(
                        "belongsto" => ["test2"],
                        "hasmany" => []
                    )
                ), //array, model description, properties and relationships
                'app_defined' => false, //boolean, model defined in app. otherwise engine
                'action'=> 'action', //string, action
                'default' => array(), //array, default actions
                'action_defined' => true, //boolean, action defined
                'action_default' => false, //boolean, action is default defined
                'action_exists' => true, //boolean, action exists in class
                'action_details' => array(
                    "action" => array(
                        "description" => "Test action",
                        "parameters" => ["description"],
                        "authenticate" => false
                    )
                ) //array, action description, parameters, exempt, authenticate
            )
        );
        //parameter_status
        $request->setParameterStatus(
            array(
                //properties supplied
                'final' => array('property'),
                //missing properties that should have been supplied
                'missing' => array(),
                //extra properties that should not have been supplied
                'extra' => array(),
                //these, especially for mongodb have to be saved as mongoids
                'ids' => array()
            )
        );
        //data
        $request->setData(
            array(
                'property' => "property's value"
            )
        );
        //meta
        //?
        //session
        //$request->setSession('user_session_token');

        $status = \iriki\engine\mongodb::doCreate($request);

        //assert
        $this->assertEquals(true, $status['message']);
    }
}

?>