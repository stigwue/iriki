<?php

namespace iriki;

class user_session extends \iriki\engine\request
{
	private static $generator = null;

	private static function generate()
	{
		if (is_null(Self::$generator)) {
			Self::$generator = (new \RandomLib\Factory)->getLowStrengthGenerator();
		}

		$data = array();

		$data['token'] = Self::$generator->generateString(14, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

		//get ip address
		if (isset($_SERVER['REMOTE_ADDR']))
		{
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$data['ip'] = '0.0.0.0';  //unknown
		}

		$data['started'] = time(NULL);

		$data['pinged'] = time(NULL);

		return $data;
	}

	public function initiate($request, $wrap = true)
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

	public function validate($request, $wrap = true)
	{
		if (!is_null($request))
	    {
	    	//read token
			$token_obj = $request->read($request, false);
			if (count($token_obj) != 0)
			{
				//token exists
				if (isset($token_obj[0]['user_id']))
				{
					//token belongs to an existing user
					$token = $token_obj[0];

					//flag
					$authenticated = true;

					//check .authenticated is true
					$authenticated = (
						$authenticated &&
						$token['authenticated'] == 'true'
					);

					//expiry check
					$stamp_expired = time(NULL);
					if ($token['remember'] == 'true')
					{
						//specified duration after last ping
						$stamp_expired = ((int)$token['pinged']) + IRIKI_SESSION_LONG;
					}
					else
					{
						//specified duration after created
						$stamp_expired = ((int)$token['pinged']) + IRIKI_SESSION_SHORT;
					}
					$authenticated = (
						$authenticated &&
						$stamp_expired > time(NULL)
					);

					if ($authenticated)
					{
						return \iriki\engine\response::information($authenticated, $wrap,
							[
								'authenticated' => $authenticated,
								'token' => $request->getData()['token'],
								'user_id' => $token['user_id'],
								'created' => $token['created'],
								'pinged' => $token['pinged']
							]
						);
					}
					else
					{
						return \iriki\engine\response::information('Token is invalid or expired', $wrap, ['token' => $request->getData()['token']]);
					}
				}
				else
				{
					//token isn't tied to an existing user
					return \iriki\engine\response::information('Token does not belong to a user', $wrap, ['token' => $request->getData()['token']]);
				}
			}
			else
			{
				//token does not exist
				return \iriki\engine\response::error('Token does not exist', $wrap, ['token' => $request->getData()['token']]);
			}
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

				return \iriki\engine\response::error('Session not found', $wrap);
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

				return \iriki\engine\response::error('Session not found', $wrap);
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

	public function read_by_token($request, $wrap = true)
	{
		$result = array(
			'session' => [
				'token' => $request->getData()['token'],
				'user_id' => null
			],
			'user' => [
				'valid' => false,
				'username' => null,
				'created' => 0
			]
		);

		if (!is_null($request))
		{
			//get session details using token
			//read username using user_id from session

			//do others else where
			$token_request = clone $request;
		  	$token_obj = $token_request->read($request, false);

		  	if (count($token_obj) != 0)
		  	{
		  		$token = $token_obj[0];

		  		if (isset($token['user_id']))
		  		{
		  			$result['session']['user_id'] = $token['user_id'];
		  			$result['user']['valid'] = true;

		  			//read user details
					$user_request = clone $request;

		  			$user_request->setModelStatus(
						array(
								'str' => 'user', //string, model
								'str_full' => '\iriki\user', //string, full model including namespace
								'defined' => true, //boolean, model defined in app or engine config
								'exists' => true, //boolean, model class exists

								'details' => $GLOBALS['APP']['models']['engine']['user'], //array, model description, properties and relationships

								'app_defined' => false, //boolean, model defined in app. otherwise engine
								'action'=> 'read', //string, action

								'default' => $GLOBALS['APP']['routes']['engine']['default'], //array, default actions

								'action_defined' => true, //boolean, action defined
								'action_default' => false, //boolean, action is default defined
								'action_exists' => true, //boolean, action exists in class

								'action_details' => $GLOBALS['APP']['routes']['engine']['routes']['user'] //array, action description, parameters, exempt
						)
					);

					$user_request->setData([
						'_id' => $token['user_id']
					]);

					$user_request->setParameterStatus([
						'final' => array('_id'),
						'missing' => array(),
						'extra' => array(),
						'ids' => array('_id')
					]);

					$user_handle = new \iriki\user();
					$user_obj = $user_handle->read($user_request, false);

					if (count($user_obj) != 0)
		  			{
		  				$user = $user_obj[0];
		  				$result['user']['username'] = $user['username'];
		  				$result['user']['created'] = $user['created'];
		  			}
				}
		 	}
		}

    	return \iriki\engine\response::data($result, $wrap);
	}

	public function read_by_user($request, $wrap = true)
	{
	    if (!is_null($request))
		{
			$data = $request->getData();
			//add authenticated
			$data['authenticated'] = true;

			//set back data
			$request->setData($data);

    		$request->setParameterStatus(array(
				'final' => array('user_id', 'authenticated'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id')
			));

			//sort by pings and created
	        $request->setMeta([
	        	'sort' => array('pinged' => -1)
	        ]);

			return $request->read($request, $wrap);
		}
	}

    public function read_anonymized($request, $wrap = true)
    {
        $data = $request->getData();

        $request->setData([]);
        
        $request->setParameterStatus(array(
          'final' => array(),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta([
        	'limit' => $data['count'],
        	'sort' => array('created' => -1)
        ]);

        $user_sessions = $request->read($request, false);

        $anon_user_sessions = array();

        //anonymize
        foreach ($user_sessions as $index => $user_session)
        {
        	unset($user_session['token']);
        	unset($user_session['remember']);
        	unset($user_session['user_id']);

        	$anon_user_sessions[] = $user_session;
        }

        return \iriki\engine\response::data($anon_user_sessions, $wrap);
    }
}

?>
