<?php

class databaseTest extends \PHPUnit\Framework\TestCase
{
    public function test_doInitialise_failure()
    {
        $db = new \iriki\engine\database();
        $status = iriki\engine\database::doInitialise(null, 'iriki');

        //assert
        $this->assertEquals(false, $status);
    }
}

?>
