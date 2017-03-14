<?php

namespace iriki;

class user_session extends \iriki\request
{
	private static function generate()
	{
		$seed = time(NULL); //uniqid(time(NULL), true);

		$data = array();

		$data['token'] = (
			\PseudoCrypt::hash($seed, 10) .
			\PseudoCrypt::hash($seed, 4)
		);

		//get ip address
		$data['ip'] = $_SERVER['SERVER_ADDR'];

		$data['started'] = time(NULL);

		return $data;
	}

	public function initiate($request)
	{
    if (!is_null($request))
    {
      $data = $request->getData();
			$parameters = $request->getParameterStatus();

			//generate token
			$data_added = Self::generate();

			$data = array_merge($data, $data_added);

			//insert these three in final parameters
			array_push($parameters['final'], 'token', 'ip', 'started');

      $request->setData($data);
      $request->setParameterStatus($parameters);

      return $request->create($request);
    }
    else
    {
      //fail gracefully some way?
    }
	}


	public function validate($request)
	{
		//read
    if (!is_null($request))
    {
			$sessions_found = $request->read($request, false);

			if (count($sessions_found) == 1)
			{

			}
			else
			{

			}
      return $sessions_found;
    }
	}

	public function invalidate($request)
	{
		if (!is_null($request))
		{
			return $request->read($request);
		}
	}

}

?>
