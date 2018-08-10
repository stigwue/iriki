<?php

namespace iriki_tests\engine;

require_once(__DIR__ . '/../../iriki/response.php');

class responseTest extends \PHPUnit\Framework\TestCase
{
	//show missing one
	public function test_showMissing_one()
	{
		$result = \iriki\engine\response::showMissing(
			array('Tomato paste'),
			array(
				'singular' => 'jollof ingredient',
				'plural' => 'jollof ingredients'
			),
			'added'
		);

		$this->assertEquals(
			"'Tomato paste' jollof ingredient added.",
			$result
		);
	}

	//show missing two
	public function test_showMissing_two()
	{
		$result = \iriki\engine\response::showMissing(
			array('Tomato paste', 'Crayfish'),
			array(
				'singular' => 'jollof ingredient',
				'plural' => 'jollof ingredients'
			),
			'missing'
		);

		$this->assertEquals(
			"'Tomato paste' and 1 other jollof ingredient missing.",
			$result
		);
	}

	//show missing more
	public function test_showMissing_more()
	{
		$result = \iriki\engine\response::showMissing(
			array('Tomato paste', 'Crayfish', 'Meat'),
			array(
				'singular' => 'jollof ingredient',
				'plural' => 'jollof ingredients'
			),
			'missing'
		);

		$this->assertEquals(
			"'Tomato paste' and 2 other jollof ingredients missing.",
			$result
		);
	}

	//build no data
	public function test_build_nodata()
	{
		$result = \iriki\engine\response::build(
			\iriki\engine\response::OK,
			'The agabara has been passed',
			null
		);

		$this->assertEquals(
			true,
			($result['code'] == \iriki\engine\response::OK) AND ($result['message'] == 'The agabara has been passed')
		);
	}

	//build with data
	public function test_build_data()
	{
		$result = \iriki\engine\response::build(
			\iriki\engine\response::OK,
			'The agabara has been passed',
			array('woli', 'agba')
		);

		$this->assertEquals(
			true,
			(isset($result['data'])) AND (count($result['data']) == 2)
		);
	}

	//buildFor wrap for unwrapped result
	public function test_buildFor_wrap()
	{
		$result = \iriki\engine\response::buildFor(
			'data',
			array('key' => 'The agabara has been passed'),
			true
		);

		$this->assertEquals(
			true,
			($result['code'] == \iriki\engine\response::OK) AND (isset($result['data']))
		);
	}

	//buildFor wrap for pre-wrapped result
	public function test_buildFor_wrap_pre()
	{
		$result = \iriki\engine\response::buildFor(
			'data',
			array(
				'code' => \iriki\engine\response::OK,
				'data' => array('key' => 'The agabara has been passed')
			),
			true
		);

		$this->assertEquals(
			true,
			($result['code'] == \iriki\engine\response::OK) AND (isset($result['data']))
		);
	}

	//buildFor no wrap for unwrapped result
	public function test_buildFor_nowrap()
	{
		$result = \iriki\engine\response::buildFor(
			'data',
			array('key' => 'The agabara has been passed'),
			false
		);

		$this->assertEquals(
			true,
			isset($result['key'])
		);
	}

	//buildFor no wrap for prewrapped result
	public function test_buildFor_nowrap_pre()
	{
		$result = \iriki\engine\response::buildFor(
			'data',
			array(
				'code' => \iriki\engine\response::OK,
				'data' => array('key' => 'The agabara has been passed')
			),
			false
		);

		$this->assertEquals(
			true,
			isset($result['key'])
		);
	}


	//data - wrap
	public function test_data_wrap()
	{
		$result = \iriki\engine\response::data(
			array(
				'key' => 'The agabara has been passed'
			),
			true
		);

		$this->assertEquals(
			true,
			(isset($result['code']) && isset($result['data']))
		);
	}

	//data - nowrap
	public function test_data_nowrap()
	{
		$result = \iriki\engine\response::data(
			array(
				'key' => 'The agabara has been passed'
			),
			false
		);

		$this->assertEquals(
			'The agabara has been passed',
			$result['key']
		);
	}

	//information/error/auth - wrap
	public function test_information_wrap()
	{
		$result = \iriki\engine\response::information(
			'The agabara has been passed',
			true
		);

		$this->assertEquals(
			true,
			(isset($result['code']) && isset($result['message']))
		);
	}

	//information/error/auth - no wrap
	public function test_information_nowrap()
	{
		$result = \iriki\engine\response::information(
			'The agabara has been passed',
			false
		);

		$this->assertEquals(
			'The agabara has been passed',
			$result
		);
	}
}

?>