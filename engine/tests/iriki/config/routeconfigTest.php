<?php

namespace iriki_tests\engine\config;

class routeConfigTest extends \PHPUnit\Framework\TestCase
{ 
    public function test_loadFromJsonIndex_success()
    {
    	$config_values = array(
			'engine' => array('path' => __DIR__ . '/files/')
		);
		
		$obj = new \iriki\engine\route_config();
		
        $result = $obj->loadFromJsonIndex($config_values);

        //assert
        $this->assertNotEquals(0, count($result['list']));
    }
    
    public function test_loadFromJsonIndex_failure()
    {
    	$config_values = array(
			'engine' => array('path' => __DIR__ . '/files/404.json')
		);
		
		$obj = new \iriki\engine\route_config();
		
        $result = $obj->loadFromJsonIndex($config_values);
        
        //assert
        $this->assertEquals(0, count($result['list']));
    }
    
    public function test_loadFromJson_success()
    {
    	$config_values = array(
			'engine' => array('path' => __DIR__ . '/files/')
		);
		
		$obj = new \iriki\engine\route_config();
		
        $result = $obj->loadFromJson(
            $config_values,
            array('list' => ['model'])
        );

        //assert
        $this->assertNotEquals(0, count($result['routes']));
    }
    
    public function test_loadFromJson_failure()
    {
        $config_values = array(
            'engine' => array('path' => __DIR__ . '/files/')
        );
        
        $obj = new \iriki\engine\route_config();
        
        $result = $obj->loadFromJson(
            $config_values,
            array('list' => ['model2'])
        );

        //assert
        $this->assertEquals(true, is_null($result['routes']['model2']));
    }
}

?>