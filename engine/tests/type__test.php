<?php

class type__test extends \PHPUnit\Framework\TestCase
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
}

?>
