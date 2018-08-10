<?php

namespace iriki_tests\engine\database\mongodb;

class nullTest extends \PHPUnit\Framework\TestCase
{
	//CRUD nulls
    public function test_doCreate_null()
    {
    	\iriki\engine\mongodb::doDestroy();

        $db_instance = \iriki\engine\mongodb::doInitialise(
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

        $status = \iriki\engine\mongodb::doCreate($request);

        //assert
        $this->assertEquals(null, $status);
    }

    public function test_doRead_null()
    {
    	\iriki\engine\mongodb::doDestroy();

        $db_instance = \iriki\engine\mongodb::doInitialise(
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

        $status = \iriki\engine\mongodb::doRead($request, array());

        //assert
        $this->assertEquals(null, $status);
    }

    public function test_doUpdate_null()
    {
    	\iriki\engine\mongodb::doDestroy();

        $db_instance = \iriki\engine\mongodb::doInitialise(
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

        $status = \iriki\engine\mongodb::doUpdate($request);

        //assert
        $this->assertEquals(null, $status);
    }

    public function test_doDelete_null()
    {
    	\iriki\engine\mongodb::doDestroy();

        $db_instance = \iriki\engine\mongodb::doInitialise(
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

        $status = \iriki\engine\mongodb::doDelete($request);

        //assert
        $this->assertEquals(null, $status);
    }

}
?>
