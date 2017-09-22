<?php

require_once(__DIR__ . '/../iriki/response.php');

class responseTest extends \PHPUnit\Framework\TestCase
{
	//show missing one
	public function test_showMissing_one()
	{
		$result = \iriki\response::showMissing(
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
		$result = \iriki\response::showMissing(
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
		$result = \iriki\response::showMissing(
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

	//build
	public function test_build_nodata()
	{
		$result = \iriki\response::build(
			\iriki\response::OK,
			'The agabara has been passed',
			null
		);

		$this->assertEquals(
			true,
			($result['code'] == \iriki\response::OK) AND ($result['message'] == 'The agabara has been passed')
		);
	}

	public function test_build_data()
	{
		$result = \iriki\response::build(
			\iriki\response::OK,
			'The agabara has been passed',
			array('woli', 'agba')
		);

		$this->assertEquals(
			true,
			(isset($result['data'])) AND (count($result['data']) == 2)
		);
	}

	//buildFor wrap unwrapped
	public function test_buildFor_wrap()
	{
		$result = \iriki\response::build(
			\iriki\response::OK,
			'The agabara has been passed',
			null
		);

		$this->assertEquals(
			true,
			($result['code'] == \iriki\response::OK) AND ($result['message'] == 'The agabara has been passed')
		);
	}

	//buildFor wrap pre-wrapped

	//buildFor nowrap unwrapped
	/*public function test_buildFor_nowrap()
	{

	}*/

	//buildFor wrap unwrapped


	//data

	//information/error/auth - wrap
	public function test_information_wrap()
	{
		$result = \iriki\response::information(
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
		$result = \iriki\response::information(
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