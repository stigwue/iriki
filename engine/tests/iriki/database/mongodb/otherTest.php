<?php

namespace iriki_engine_tests;

class otherTest extends \PHPUnit\Framework\TestCase
{
    public function test_isMongoId_failure()
    {
        $false_mongo_id = 'false mongo id';

        $status = \iriki\engine\mongodb::isMongoId($false_mongo_id);

        //assert
        $this->assertNotEquals(true, $status);
    }

    public function test_isMongoId_success()
    {
        $true_mongo_id = '596cbd52565bb550080041b8';

        $status = \iriki\engine\mongodb::isMongoId($true_mongo_id);

        //assert
        $this->assertEquals(true, $status);
    }

    public function test_doInitialise_failure()
    {
        $status = \iriki\engine\mongodb::doInitialise(
            null
        );

        //assert
        $this->assertEquals(null, $status);
    }

    //session token check: auth, remember, ip?

    //enforce ids

    //de enforce

}

?>
