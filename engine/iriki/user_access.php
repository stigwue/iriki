<?php

namespace iriki;

class user_access extends \iriki\request
{
	public function user_in_group($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('user_id', 'user_group_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id', 'user_group_id')
			));
			
			$data = $request->read($request, false);

			if (count($data) != 0)
			{
				return \iriki\response::information('true', $wrap);
			}
			else {
				return \iriki\response::information('false', $wrap);
			}
		}
	}

	public function remove_user($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('user_id', 'user_group_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id', 'user_group_id')
			));
			
			$status = $request->delete($request, false);

			if ($status)
			{
				return \iriki\response::information('true', $wrap);
			}
			else {
				return \iriki\response::information('false', $wrap);
			}
		}
	}
}

?>