<?php

namespace iriki;

class user extends \iriki\request
{
	public function signup($request, $wrap = true)
	{
		if (!is_null($request))
		{
		  $data = $request->getData();

		  //note, we assume that for signup, hash contains plaintext auth
		  $data['hash'] = password_hash($data['hash'], PASSWORD_BCRYPT);

		  $request->setData($data);

		  return $request->create($request, $wrap);
		}
		else
		{
		  //fail gracefully some way?
		}
	}

	public function authenticate($request, $wrap = true)
	{
		if (!is_null($request))
		{
			$new_request = clone $request;
		  	$data = $new_request->getData();

		  	$new_request->setData(
				array(
					'username' => $data['username']
				)
			);
			$new_request->setParameterStatus(array(
				'final' => array('username'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			//read by username solely
			$result = $new_request->read($new_request, false);

			$authenticated = false;

			//create a session using a request
			//session details will depend on the status of user authenticate
			$session_request = clone $request;

			$session_request->setModelStatus(
				array(
						'str' => 'user_session', //string, model
						'str_full' => '\iriki\user_session', //string, full model including namespace
						'defined' => true, //boolean, model defined in app or engine config
						'exists' => true, //boolean, model class exists

						'details' => $GLOBALS['APP']['models']['engine']['user_session'], //array, model description, properties and relationships

						'app_defined' => false, //boolean, model defined in app. otherwise engine
						'action'=> 'initiate', //string, action

						'default' => $GLOBALS['APP']['routes']['engine']['default'], //array, default actions

						'action_defined' => true, //boolean, action defined
						'action_default' => false, //boolean, action is default defined
						'action_exists' => true, //boolean, action exists in class

						'action_details' => $GLOBALS['APP']['routes']['engine']['routes']['user_session'] //array, action description, parameters, exempt
				)
			);

			if (count($result) == 0)
			{
				//user not found
				//create a session which isn't logged on
				//that is if we plan to convert unsigned in users

				//change data
				$session_request->setData(
					array(
						'user_id' => '0',
						'authenticated' => 'false',
						'remember' => 'true'
					)
				);
				//change parameters
				$session_request->setParameterStatus(array(
					'final' => array('authenticated', 'remember'),
					'missing' => array(),
					'extra' => array(),
					'ids' => array('user_id')
				));

				$user_session = new \iriki\user_session();
				$status = $user_session->initiate($session_request);

				//save token?
				$token = $session_request->getData()['token'];
				return \iriki\response::data($token, $wrap);
			}
			else
			{
				//user found
				$single_result = $result[0];
				$hash = $single_result['hash'];
				$authenticated = (password_verify($data['hash'], $hash) !== FALSE);

				//change data
				$session_request->setData(
					array(
						'user_id' => $single_result['_id'],
						'authenticated' => ($authenticated ? 'true' : 'false'),
						'remember' => 'true'
					)
				);
				//change parameters
				$session_request->setParameterStatus(array(
					'final' => array('authenticated', 'remember'),
					'missing' => array(),
					'extra' => array(),
					'ids' => array('user_id')
				));

				$user_session = new \iriki\user_session();
				$status = $user_session->initiate($session_request);

				//save token?
				$token = $session_request->getData()['token'];
				return \iriki\response::data($token, $wrap);
			}
		}
	}

	public function change_auth($request, $wrap = true)
	{
		if (!is_null($request))
		{
			$new_request = clone $request;
		  	$data = $new_request->getData();

		  	$new_request->setData(
				array(
					'username' => $data['username']
				)
			);
			$new_request->setParameterStatus(array(
				'final' => array('username'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			//read by username solely
			$result = $new_request->read($new_request, false);

			if (count($result) != 0)
			{
				//user found
				$single_result = $result[0];
				$hash = $single_result['hash'];
				$authenticated = (password_verify($data['hash_old'], $hash) !== FALSE);

				if ($authenticated)
				{
					$new_hash = password_hash($data['hash_new'], PASSWORD_BCRYPT);

					$change_request = clone $request;
				  	$data = $single_result;

				  	//change some properties
				  	$data['hash'] = $new_hash;
				  	$change_request->setData($data);
					$change_request->setParameterStatus(
						array(
							'final' => array('_id', 'username', 'hash', 'created'),
							'missing' => array(),
							'extra' => array(),
							'ids' => array('_id')
						)
					);

					//save new auth
					return $change_request->update($change_request, $wrap);
				}
				else
				{
					//authentication failed
				}
			}

			return \iriki\response::error('Authentication change failed.', $wrap);
		}
	}
}

?>
