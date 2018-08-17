<?php

namespace iriki_tests\engine\database\mongo;

class otherTest extends \PHPUnit\Framework\TestCase
{
    public function test_isMongoId_failure()
    {
        $false_mongo_id = 'false mongo id';

        $status = \iriki\engine\mongo::isMongoId($false_mongo_id);

        //assert
        $this->assertNotEquals(true, $status);
    }

    public function test_isMongoId_success()
    {
        $true_mongo_id = '596cbd52565bb550080041b8';

        $status = \iriki\engine\mongo::isMongoId($true_mongo_id);

        //assert
        $this->assertEquals(true, $status);
    }

    public function test_doInitialise_failure()
    {
        $status = \iriki\engine\mongo::doInitialise(
            null
        );

        //assert
        $this->assertEquals(null, $status);
    }

    //connection string
    public function test_buildConnString_default()
    {
        $status = \iriki\engine\mongo::buildConnString([
            'server' => 'mongodb://server:27017',
            'db' => 'db'
        ]);

        //assert
        $this->assertEquals('mongodb://server:27017', $status);
    }

    public function test_buildConnString_server_no_port()
    {
        $status = \iriki\engine\mongo::buildConnString([
            'server' => 'server',
            'port' => 1024,
            'db' => 'db'
        ]);

        //assert
        $this->assertEquals('mongodb://server:1024', $status);
    }

    public function test_buildConnString_auth()
    {
        $status = \iriki\engine\mongo::buildConnString([
            'server' => 'server',
            'port' => 1023,
            'user' => 'user',
            'password' => 'p455w0rd',
            'db' => 'db'
        ]);

        //assert
        $this->assertEquals('mongodb://user:p455w0rd@server:1023', $status);
    }


    //session token check: auth, remember, ip?

    //enforce ids

    //de enforce

}

?>
