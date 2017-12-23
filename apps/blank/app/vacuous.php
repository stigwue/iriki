<?php

namespace blank;

class vacuous extends \iriki\request
{
	public static function no_auth($request, $wrap = true)
	{
		return \iriki\response::data(['data' => 'needed'], $wrap, 'some message');
	}

	public static function auth($request, $wrap = true)
	{
		return \iriki\response::information('some message', $wrap, ['extra' => 'data']);
	}
}

?>