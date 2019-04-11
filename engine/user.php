<?php

namespace iriki;

class user extends \iriki\engine\request
{
	private static $generator = null;

	private static function generate()
	{
		$seed = time(NULL);

		$data = array();

		if (is_null(Self::$generator)) {
			Self::$generator = (new \RandomLib\Factory)->getLowStrengthGenerator();
		}

		return Self::$generator->generateString(6);

	}

	public function read_by_username($request, $wrap = true)
	{
	    if (!is_null($request))
		{
	      	$request->setParameterStatus(array(
				'final' => array('username'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			$users = $request->read($request, false);

			if (count($users) == 0)
			{
				return \iriki\engine\response::error('User not found.', $wrap);
			}
			else
			{
				return \iriki\engine\response::data($users, $wrap);
			}
		}
	}

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
			//clone request
			$new_request = clone $request;
		  	$data = $new_request->getData();
		  	$remember = ($data['remember'] == 'true');

		  	//set new request to read solely by username
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

			//create a new session using old request
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

			//check if user with username was found
			if (count($result) == 0)
			{
				//user not found
				//create a session which isn't logged on
				//that is if we plan to convert unsigned in users

				//change data
				$session_request->setData(
					array(
						'user_id' => '0',
						'authenticated' => false,
						'remember' => $remember
					)
				);
				//change parameters
				$session_request->setParameterStatus(array(
					'final' => array('authenticated', 'remember', 'user_id'),
					'missing' => array(),
					'extra' => array(),
					'ids' => array('user_id')
				));

				$user_session = new \iriki\user_session();
				//this session will not be created, user_id is not a valid id
				$status = $user_session->initiate($session_request);

				//save token?
				$token = $session_request->getData()['token'];
				return \iriki\engine\response::data($token, $wrap, $authenticated);
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
						'authenticated' => ($authenticated ? true : false),
						'remember' => $remember
					)
				);
				//change parameters
				$session_request->setParameterStatus(array(
					'final' => array('authenticated', 'remember', 'user_id'),
					'missing' => array(),
					'extra' => array(),
					'ids' => array('user_id')
				));

				$user_session = new \iriki\user_session();
				$status = $user_session->initiate($session_request);

				//save token?
				$token = $session_request->getData()['token'];
				return \iriki\engine\response::data($token, $wrap, $authenticated);
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

			return \iriki\engine\response::error('Authentication change failed.', $wrap);
		}
	}

	public function reset_auth($request, $wrap = true)
	{
		if (!is_null($request))
		{
			$params = $request->getData();

			$request1 = array(
	            'code' => 200,
	            'message' => '',
	            'data' => array(
	                'model' => 'user',
	                'action' => 'read_by_username',
	                'url_parameters' => array(),
	                'params' => array(
	                    'username' => $params['username']
	                )
	            )
	        );

	        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request1);

	        $status1 = \iriki\engine\route::matchRequestToModel(
	            $GLOBALS['APP'],
	            $model_profile,
	            $request1,
	            true
	        );

	        if ($status1['code'] == 200 AND is_array($status1['data']))
	        {
	        	if (count($status1['data']) == 1)
	        	{
	        		$user = $status1['data'][0];

	        		//skip verification, reset it
					$new_password = Self::generate();

					$request2 = array(
			            'code' => 200,
			            'message' => '',
			            'data' => array(
			                'model' => 'user',
			                'action' => 'update_auth',
			                'url_parameters' => array(),
			                'params' => array(
			                    '_id' => $user['_id'],
			                    'hash' => $new_password
			                )
			            )
			        );

			        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request2);

			        $status2 = \iriki\engine\route::matchRequestToModel(
			            $GLOBALS['APP'],
			            $model_profile,
			            $request2,
			            true
			        );

					//save new auth
					if ($status2['code'] == 200 AND $status2['message'] == true)
					{
						return \iriki\engine\response::information($new_password, $wrap);
					}
	        	}
	        }
	        return \iriki\engine\response::error('Authentication reset failed.', $wrap);
		}
	}

	public function update_auth($request, $wrap = true)
	{
	    if (!is_null($request))
		{
			$params = $request->getData();

			$new_hash = password_hash($params['hash'], PASSWORD_BCRYPT);

			$request->setData([
				'_id' => $params['_id'],
				'hash' => $new_hash
			]);
    		
    		$request->setParameterStatus(array(
				'final' => array('_id', 'hash'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('_id')
			));

			return $request->update($request, $wrap);
		}
	}				

	public function delete($request, $wrap = true)
	{
	    if (!is_null($request))
		{
    		$request->setParameterStatus(array(
				'final' => array('_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('_id')
			));
			return $request->delete($request, $wrap);
		}
	}

	public function delete_by_username($request, $wrap= true)
	{
	    if (!is_null($request))
		{
    		$request->setParameterStatus(array(
				'final' => array('username'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));
			return $request->delete($request, $wrap);
		}
	}
}
?>
