<?php

namespace iriki;

class user_session extends \iriki\request
{
	private static $generator = null;

	private static function generate()
	{
		if (is_null(Self::$generator)) {
			Self::$generator = (new \RandomLib\Factory)->getLowStrengthGenerator();
		}

		$data = array();

		$data['token'] = Self::$generator->generateString(14);

		//get ip address
		$data['ip'] = $_SERVER['SERVER_ADDR'];

		$data['started'] = time(NULL);

		$data['pinged'] = time(NULL);

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
			array_push($parameters['final'], 'token', 'ip', 'started', 'pinged');

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
			return $request->read($request, true);
	    }
	}

	public function ping($request, $wrap = true)
	{
		if (!is_null($request))
		{

			//get the session details first
			$sessions_found = $request->read($request, false);

			if (count($sessions_found) == 0)
			{
				//this session wasn't found, return error?

				return \iriki\response::error('Session not found', $wrap);
			}
			else
			{
				$session = $sessions_found[0];
				//invalidate
				$data = $session;
				$data['pinged'] = time(NULL);

				$request->setData($data);
				$request->setParameterStatus(
					array(
						'final' => array('_id', 'authenticated', 'remember', 'token', 'ip', 'started', 'pinged', 'user_id', 'created'),
						'missing' => array(),
						'extra' => array(),
						'ids' => array('_id', 'user_id')
					)
				);

				return $request->update($request, $wrap);
			}
		}
	}

	public function invalidate($request, $wrap = true)
	{
		if (!is_null($request))
		{

			//get the session details first
			$sessions_found = $request->read($request, false);

			if (count($sessions_found) == 0)
			{
				//this session wasn't found, return error?

				return \iriki\response::error('Session not found', $wrap);
			}
			else
			{
				$session = $sessions_found[0];
				//invalidate
				$data = $session;
				$data['authenticated'] = false;

				$request->setData($data);
				$request->setParameterStatus(
					array(
						'final' => array('_id', 'authenticated', 'remember', 'token', 'ip', 'started', 'user_id', 'created'),
						'missing' => array(),
						'extra' => array(),
						'ids' => array('_id', 'user_id')
					)
				);

				return $request->update($request, $wrap);
			}
		}
	}

	public function read_by_token($request)
	{
	    if (!is_null($request))
	    {
			return $request->read($request, true);
	    }
	}
}

?>
