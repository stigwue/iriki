<?php

namespace blank;

class vacant extends \iriki\request
{
	public static function no_auth($request, $wrap = true)
	{
		return \iriki\response::data(['data' => 'needed'], $wrap, 'some message');
	}
}

?>