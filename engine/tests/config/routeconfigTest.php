<?php

class routeconfigTest extends \PHPUnit\Framework\TestCase
{ 
    public function test_loadFromJsonIndex_success()
    {
    	$config_values = array(
			'engine' => array('path' => __DIR__ . '/files/')
		);
		
		$obj = new iriki\route_config();
		
        $result = $obj->loadFromJsonIndex($config_values);

        //assert
        $this->assertNotEquals(0, count($result['list']));
    }
    
    public function test_loadFromJsonIndex_failure()
    {
    	$config_values = array(
			'engine' => array('path' => __DIR__ . '/files/404.json')
		);
		
		$obj = new iriki\route_config();
		
        $result = $obj->loadFromJsonIndex($config_values);
        
        //assert
        $this->assertEquals(0, count($result['list']));
    }
    
    public function test_loadFromJson_success()
    {
    	$config_values = array(
			'engine' => array('path' => __DIR__ . '/files/')
		);
		
		$obj = new iriki\route_config();
		
        $result = $obj->loadFromJson(
            $config_values,
            array('list' => ['model'])
        );

        //assert
        $this->assertNotEquals(0, count($result['routes']));
    }
}

?>