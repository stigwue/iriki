<?php

namespace iriki_tests\engine;

require_once(__DIR__ . '/../../iriki/response.php');
require_once(__DIR__ . '/../../iriki/request.php');

class requestTest extends \PHPUnit\Framework\TestCase
{
	public function test_catchError_error_wrap()
	{
		$result = \iriki\engine\request::catchError(
			//result
			array(
				'message' => true,
				'data' => "59cb9986565bb51b740041a7"
			),
			//default_response: info,data or error
			'data',
			//wrap
			true,
			//log object
			null,
			//perform log?
			false
		);

		$this->assertEquals(true,
			(isset($result['result']['code']) AND $result['result']['code'] == \iriki\engine\response::OK AND
			isset($result['result']['message']) AND isset($result['result']['data']))
		);
	}

	public function test_catchError_error_nowrap()
	{
		$result = \iriki\engine\request::catchError(
			//result
			'Funke! Some error occurred!!',
			//default_response: info,data or error
			'information',
			//wrap
			false,
			//log object
			null,
			//perform log?
			false
		);

		$this->assertEquals(
			'Funke! Some error occurred!!',
			$result['result']
		);
	}

	public function test_catchError_noerror()
	{
		$result = \iriki\engine\request::catchError(
			//result
			array('data' => ['property' => 'minutae']),
			//default_response: info,data or error
			'data',
			//wrap
			true,
			//log object
			null,
			//perform log?
			false
		);

		$this->assertEquals(true,
			(
				isset($result['result']['data']) AND is_array($result['result']['data'])
			)
		);
	}
}

?>