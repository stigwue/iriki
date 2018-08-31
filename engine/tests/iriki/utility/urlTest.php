<?php

namespace iriki_tests\engine;

class urlTest extends \PHPUnit\Framework\TestCase
{
	public function test_parse_url_success()
	{
		$status = \iriki\engine\url::parseUrl('http://iriki/model/route/url_par1/url_par2?q1=v1&q2=v2');

		$this->assertEquals(true,
			(count($status['parts']) == 4) AND
			(count($status['parameters']) == 2) AND
			($status['query'] == 'q1=v1&q2=v2')
		);

		$query = $status['query'];

		return $query;
	}

	/**
     * @depends test_parse_url_success
     */
    public function test_parse_get_params_success($query)
	{
		$status = \iriki\engine\url::parseGetParams($query);

		$this->assertEquals(true,
			(count($status) == 2) AND
			(isset($status['q1'])) AND
			($status['q1'] == 'v1')
		);
	}

	public function txst_make_request_success()
	{
		$status = \iriki\engine\url::makeRequest('http://iriki/log/summary');

		var_dump(json_decode($status));
	}
}

?>