<?php

namespace iriki_engine_tests;

class nullTest extends \PHPUnit\Framework\TestCase
{
	//CRUD nulls
    public function test_doCreate_null()
    {
    	\iriki\engine\mongo::doDestroy();

        $db_instance = \iriki\engine\mongo::doInitialise(
            null
        );

        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
        //model status
        $request->setModelStatus(
            null
        );
        //parameter_status
        $request->setParameterStatus(
            null
        );
        //data
        $request->setData(
            null
        );
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongo::doCreate($request);

        //assert
        $this->assertEquals(null, $status);
    }

    public function test_doRead_null()
    {
    	\iriki\engine\mongo::doDestroy();

        $db_instance = \iriki\engine\mongo::doInitialise(
            null
        );

        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
        //model status
        $request->setModelStatus(
            null
        );
        //parameter_status
        $request->setParameterStatus(
            null
        );
        //data
        $request->setData(
            null
        );
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongo::doRead($request, array());

        //assert
        $this->assertEquals(null, $status);
    }

    public function test_doUpdate_null()
    {
    	\iriki\engine\mongo::doDestroy();

        $db_instance = \iriki\engine\mongo::doInitialise(
            null
        );

        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
        //model status
        $request->setModelStatus(
            null
        );
        //parameter_status
        $request->setParameterStatus(
            null
        );
        //data
        $request->setData(
            null
        );
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongo::doUpdate($request);

        //assert
        $this->assertEquals(null, $status);
    }

    public function test_doDelete_null()
    {
    	\iriki\engine\mongo::doDestroy();

        $db_instance = \iriki\engine\mongo::doInitialise(
            null
        );

        $request = new \iriki\engine\request();
        //db_instance
        $request->setDBInstance($db_instance);
        //model status
        $request->setModelStatus(
            null
        );
        //parameter_status
        $request->setParameterStatus(
            null
        );
        //data
        $request->setData(
            null
        );
        //meta
        //?
        //session
        $request->setSession('user_session_token');

        $status = \iriki\engine\mongo::doDelete($request);

        //assert
        $this->assertEquals(null, $status);
    }

}
?>