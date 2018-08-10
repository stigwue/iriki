<?php

namespace iriki_tests\engine\database\mongodb;

class errorTest extends \PHPUnit\Framework\TestCase
{
    //CRUD errors
    //note that errors can be a issing session token
    //or a missing parameter

    public function test_database_test_config()
    {
        //assert
        $this->assertEquals(true, isset($GLOBALS['APP']['config']['database']['test']));

        $db_instance = \iriki\engine\mongodb::doInitialise(
            $GLOBALS['APP']['config']['database']['test']
        );

        return $db_instance;
    }

    /**
     * @depends test_database_test_config
     */
    public function test_doCreate_error($db_instance)
    {
        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
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
                        "authenticate" => true
                    )
                ) //array, action description, parameters, exempt, authenticate
            )
        );
        //parameter_status
        $request->setParameterStatus(
            array(
                //properties supplied
                'final' => array(),
                //missing properties that should have been supplied
                'missing' => array('property'),
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
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongodb::doCreate($request);

        //assert
        $this->assertEquals(true, isset($status['code']));
    }

    /**
     * @depends test_database_test_config
     */
    public function test_doRead_error($db_instance)
    {
        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
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
                        "parameters" => [],
                        "exempt" => ['*'],
                        "authenticate" => true
                    )
                ) //array, action description, parameters, exempt, authenticate
            )
        );
        //parameter_status
        $request->setParameterStatus(
            array(
                //properties supplied
                'final' => array(),
                //missing properties that should have been supplied
                'missing' => array('property'),
                //extra properties that should not have been supplied
                'extra' => array(),
                //these, especially for mongodb have to be saved as mongoids
                'ids' => array()
            )
        );
        //data
        $request->setData(array());
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongodb::doRead($request, array());

        //assert
        $this->assertEquals(true, isset($status['code']));
    }

    /**
     * @depends test_database_test_config
     */
    public function test_doUpdate_error($db_instance)
    {
        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
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
                        "parameters" => [],
                        "exempt" => ['*'],
                        "authenticate" => true
                    )
                ) //array, action description, parameters, exempt, authenticate
            )
        );
        //parameter_status
        $request->setParameterStatus(
            array(
                //properties supplied
                'final' => array(),
                //missing properties that should have been supplied
                'missing' => array('property'),
                //extra properties that should not have been supplied
                'extra' => array(),
                //these, especially for mongodb have to be saved as mongoids
                'ids' => array()
            )
        );
        //data
        $request->setData(array());
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongodb::doUpdate($request);

        //assert
        $this->assertEquals(true, isset($status['code']));
    }

    /**
     * @depends test_database_test_config
     */
    public function test_doDelete_error($db_instance)
    {
        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
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
                        "parameters" => [],
                        "exempt" => ['*'],
                        "authenticate" => true
                    )
                ) //array, action description, parameters, exempt, authenticate
            )
        );
        //parameter_status
        $request->setParameterStatus(
            array(
                //properties supplied
                'final' => array(),
                //missing properties that should have been supplied
                'missing' => array('property'),
                //extra properties that should not have been supplied
                'extra' => array(),
                //these, especially for mongodb have to be saved as mongoids
                'ids' => array()
            )
        );
        //data
        $request->setData(array());
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongodb::doDelete($request);

        //assert
        $this->assertEquals(true, isset($status['code']));
    }
}

?>
