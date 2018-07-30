<?php

namespace iriki_tests\engine\config;

class configTest extends \PHPUnit\Framework\TestCase
{ 
    //load_json_file failure test
    public function test_load_json_file_failure()
    {
        $contents = \iriki\engine\config::load_json_file(__DIR__ . '/files/404.json');

        //assert
        $this->assertEquals(null, $contents);
    }

    //load_json_file success test
	public function test_load_json_file_success()
    {
        $contents = \iriki\engine\config::load_json_file(__DIR__ . '/files/app.json');

        //assert
        $this->assertNotEquals(null, $contents);
    }

    //parse_json_string fail test
    public function test_parse_json_string_failure()
    {
        $non_json_string = 'non json string';

        $json = \iriki\engine\config::parse_json_string($non_json_string);

        //assert
        $this->assertEquals(null, $json);
    }

    //parse_json_string success test
    public function test_parse_json_string_success()
    {
        $json_string = '{
          "iriki":
          {
            "app" : {
              "base" : "",
              "base_url" : "/iriki/"
            }
          }
        }';

        $json = \iriki\engine\config::parse_json_string($json_string);

        //assert
        $this->assertEquals(true, is_array($json));
    }
}

?>
