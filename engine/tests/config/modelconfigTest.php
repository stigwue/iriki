<?php

class modelconfigTest extends \PHPUnit\Framework\TestCase
{
    public function test_loadFromJson_success()
    {
        $config_values = array(
            'engine' => array('path' => __DIR__ . '/files/')
        );
        
        $obj = new iriki\model_config();
        
        $result = $obj->loadFromJson(
            $config_values,
            array(
                'model' => array(
                    'action' => [
                        'description' => 'Some description',
                        'parameters' => []
                    ]
                )
            )
        );

        //assert
        $this->assertEquals(true, isset($result['model']['relationships']));
    }
}

?>