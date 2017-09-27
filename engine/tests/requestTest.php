<?php

require_once(__DIR__ . '/../iriki/response.php');
require_once(__DIR__ . '/../iriki/request.php');

class requestTest extends \PHPUnit\Framework\TestCase
{
	public function test_catchError_error_wrap()
	{
		$result = \iriki\request::catchError(
			//result
			array(
				'message' => true,
				'data' => "59cb9986565bb51b740041a7"
			),
			//default_response: info,data or error
			'data',
			//wrap
			true
		);

		$this->assertEquals(true,
			(isset($result['code']) AND $result['code'] == \iriki\response::OK AND
			isset($result['message']) AND isset($result['data']))
		);
	}

	public function test_catchError_error_nowrap()
	{
		$result = \iriki\request::catchError(
			//result
			'Funke! Some error occurred!!',
			//default_response: info,data or error
			'information',
			//wrap
			false
		);

		$this->assertEquals(
			'Funke! Some error occurred!!',
			$result
		);
	}

	public function test_catchError_noerror()
	{
		$result = \iriki\request::catchError(
			//result
			array('data' => ['property' => 'minutae']),
			//default_response: info,data or error
			'data',
			//wrap
			true
		);

		$this->assertEquals(true,
			(
				isset($result['data']) AND is_array($result['data'])
			)
		);
	}
}

?>