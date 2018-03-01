<?php

class typeTest extends \PHPUnit\Framework\TestCase
{
	public function test_is_number()
    {
    	$number = 7;
    	$this->assertEquals(true, \iriki\type::is_type($number, 'number'));
    }

	public function test_is_not_number()
    {
    	$not_number = 'a7';
    	$this->assertEquals(false, \iriki\type::is_type($not_number, 'number'));
    }

    public function test_is_email()
    {
    	$email = 'email@provider';
    	$this->assertEquals(true, \iriki\type::is_type($email, 'email'));
    }

	public function test_is_not_email()
    {
    	$not_email = 'email';
    	$this->assertEquals(false, \iriki\type::is_type($not_email, 'email'));
    }

    public function test_is_boolean()
    {
        $bool = true; //or 'true'
        $this->assertEquals(true, \iriki\type::is_type($bool, 'boolean'));
    }

    public function test_is_not_boolean()
    {
        $not_bool = 'not_bool';
        $this->assertEquals(false, \iriki\type::is_type($not_bool, 'boolean'));
    }

    public function test_is_key()
    {
        $key = '5a1d84ee0f6d2f1d160041a7';
        $this->assertEquals(true, \iriki\type::is_type($key, 'key'));
    }

    public function test_is_not_key()
    {
        $not_key = 'not_5a1d84ee0f6d2f1d160041a7';
        $this->assertEquals(false, \iriki\type::is_type($not_key, 'key'));
    }

    //convert
}

?>
