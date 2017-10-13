<?php

class modelconfigTest extends \PHPUnit\Framework\TestCase
{
    public function test_loadFromJson_success()
    {
    	$config_values = array(
			'engine' => array('path' => ''),
			'application' => array('path' => '')
		);
		
		$obj = new model_config();
		
        $contents = iriki\config::load_json_file(__DIR__ . '/files/404.json');

        //assert
        $this->assertEquals(null, $contents);
    }
}

?>