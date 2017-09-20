<?php

class defaultTest extends \PHPUnit\Framework\TestCase
{
    public function test_doInitialise_failure()
    {
        echo '!!!!!!';
        var_dump(class_exists('\iriki\engine::database'));
        $db = new \iriki\engine\database();
        $status = iriki\engine\database::doInitialise(null, 'iriki');

        //assert
        $this->assertEquals(true, $status);
    }
}

?>
