<?php

namespace iriki;

class user_group extends \iriki\request
{
	public function exists($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('title'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));
			
			$data = $request->read($request, false);

			if (count($data) != 0)
			{
				return \iriki\response::information(true, $wrap);
			}
			else {
				return \iriki\response::information(false, $wrap);
			}
		}
	}

	public function read_by_title($request, $wrap = true)
	{
    	if (!is_null($request))
	    {
			return $request->read($request, true);
	    }
	}
}

?>