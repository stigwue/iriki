<?php

class config__test extends \PHPUnit\Framework\TestCase
{   
    //load_json_file success test
	public function test_load_json_file_success()
    {
        $obj = new iriki\config('files/success.json');

        $json = $obj->getJson();

    	//assert
        $this->assertNotEquals(null, $json);
    }


    //loaad_json_file failure test
    public function test_load_json_file_failure()
    {
        $obj = new iriki\config('files/404.json');

        $json = $obj->getJson();

        //assert
        $this->assertEquals(true, (strlen($json) == 0));
    }

    //parse_json_string success test

    //parse_json_string fail test
}

?>
