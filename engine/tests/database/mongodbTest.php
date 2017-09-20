<?php

class mongodbTest extends \PHPUnit\Framework\TestCase
{
    public function test_doInitialise_success()
    {
        $status = iriki\engine\database::doInitialise(
            'kronos',
            'iriki',
            array(
                'type' => 'mongodb',
                'server' => 'mongodb://localhost:27017',
                'db' => 'kronos'
            )
        );

        //assert
        $this->assertEquals(true, $status);
    }

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

    public function test_initialize_failure()
    {
        iriki\engine\database::doInitialise(
            'kronos',
            'iriki',
            array(
                'type' => 'mongodb',
                'server' => 'mongodb://localhost:27017',
                'db' => 'kronos'
            )
        );

        $status = \iriki\engine\mongodb::initialize();

        //assert
        $this->assertEquals(true, $status);
    }
}

?>
