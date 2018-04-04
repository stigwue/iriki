<?php

namespace iriki_tests;

class uploadTest extends \PHPUnit\Framework\TestCase
{
	
	public function test_upload_dir_set()
    {
    	$upload_dir_set = isset($GLOBALS['APP']['config']['constants']['upload_dir']);

    	$this->assertEquals(true, $upload_dir_set);

    	return $upload_dir_set;
    }

    /**
	 * @depends test_upload_dir_set
     */
	public function test_upload_dir($upload_dir_set)
    {
    	if ($upload_dir_set)
    	{
	    	$upload_dir = \iriki\upload::get_upload_dir();

	    	$this->assertNotEquals(null, $upload_dir);

	    	return $upload_dir;
    	}
    }
	
	public function test_upload_http_set()
    {
    	$upload_http_set = isset($GLOBALS['APP']['config']['constants']['upload_http']);

    	$this->assertEquals(true, $upload_http_set);

    	return $upload_http_set;
    }

    
}

?>
