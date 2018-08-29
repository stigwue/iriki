<?php

namespace iriki;

class auth extends \iriki\engine\request
{
	private static $generator = null;

	private static function generate()
	{
		if (is_null(Self::$generator)) {
			Self::$generator = (new \RandomLib\Factory)->getLowStrengthGenerator();
		}

		$data = array(
			'key_short' => Self::$generator->generateString(7, '0123456789'),
			'key_long' => Self::$generator->generateString(14, '0123456789abcdefghijklmnopqrstuvwxyz')
		);

		return $data;
	}

	public function initiate($request, $wrap = true)
	{
	    if (!is_null($request))
	    {
			$data = $request->getData();

			//generate keys
			$keys = Self::generate();

			//add key to data to write
			if (strtolower($data['key_type']) == 'short')
			{
				$data['key'] = $keys['key_short'];
			}
			else
			{
				$data['key'] = $keys['key_long'];
			}
			//drop key_type
			unset($data['key_type']);
			//update parameter status
			$request->setParameterStatus([
				'final' => array('key', 'ttl', 'status', 'tag', 'user_id'),
				'extra' => array(),
				'missing' => array(),
				'ids' => array('user_id')
			]);

			$request->setData($data);

			//creates by default, return an _id, reduce roundtrip by providing the key instead
			$status = $request->create($request);

			if ($status['code'] == 200 AND $status['message'] == true)
			{
				//read
				$req = array(
	            'code' => 200,
	            'message' => '',
	            'data' => array(
	                'model' => 'auth',
	                'action' => 'read',
	                'url_parameters' => array(),
	                'params' => array(
	                	'_id' => $status['data']
	                )
	            )
		        );

		        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

		        $state = \iriki\engine\route::matchRequestToModel(
		            $GLOBALS['APP'],
		            $model_profile,
		            $req,
		            $request->getTestMode(),
		            $request->getSession()
		        );

		        if ($state['code'] == 200)
		        {
		        	$found = $state['data'];
		        	if (count($found) != 0)
		        	{
		        		$found = $found[0];
		        		return \iriki\engine\response::data($found['key'], $wrap);
		        	}
		        	else
		        	{
		        		//just return good old _id
		        		return \iriki\engine\response::data($status['data'], $wrap);
		        	}
		        }
		    }
		    else
		    {
	      		return \iriki\engine\response::error('Failed to initiate auth key.', $wrap);
		    }
	    }
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
	}

	public function revoke($request, $wrap = true)
	{
		if (!is_null($request))
		{
			//get details first
			$sessions_found = $request->read($request, false);

			if (count($sessions_found) == 0)
			{
				//this session wasn't found, return error?

				return \iriki\engine\response::error('Key not found.', $wrap);
			}
			else
			{
				$session = $sessions_found[0];
				//revoke
				$data = $session;
				$data['status'] = false;

				$request->setData($data);
				$request->setParameterStatus(
					array(
						'final' => array('_id', 'key', 'ttl', 'status', 'tag', 'user_id', 'created'),
						'missing' => array(),
						'extra' => array(),
						'ids' => array('_id', 'user_id')
					)
				);

				//revoke all underlying user session tokens
				$req = array(
	            'code' => 200,
	            'message' => '',
	            'data' => array(
	                'model' => 'user_session',
	                'action' => 'invalidate_by_user',
	                'url_parameters' => array(),
	                'params' => array(
	                	'user_id' => $data['user_id']
	                )
	            )
		        );

		        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

		        $state = \iriki\engine\route::matchRequestToModel(
		            $GLOBALS['APP'],
		            $model_profile,
		            $req,
		            $request->getTestMode(),
		            $request->getSession()
		        );

				return $request->update($request, $wrap);
			}
		}
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
	}

	public function extend($request, $wrap = true)
	{
		if (!is_null($request))
		{
			$parameters = $request->getData();

			//get details first
			//drop extend param
			$request->setData(['key' => $parameters['key']]);
			$request->setParameterStatus(
				array(
					'final' => array('key'),
					'missing' => array(),
					'extra' => array(),
					'ids' => array()
				)
			);
			//find it
			$sessions_found = $request->read($request, false);

			if (count($sessions_found) == 0)
			{
				//this session wasn't found, return error?

				return \iriki\engine\response::error('Key not found.', $wrap);
			}
			else
			{
				$session = $sessions_found[0];
				//extend
				$data = $session;
				$data['ttl'] = $data['ttl'] + $parameters['ttl_extend_by'];

				$request->setData($data);
				$request->setParameterStatus(
					array(
						'final' => array('_id', 'key', 'ttl', 'status', 'tag', 'user_id', 'created'),
						'missing' => array(),
						'extra' => array(),
						'ids' => array('_id', 'user_id')
					)
				);

				return $request->update($request, $wrap);
			}
		}
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
	}

	public function read_by_key($request, $wrap = true)
	{
	    if (!is_null($request))
		{
			$request->setParameterStatus(array(
				'final' => array('key'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			return $request->read($request, $wrap);
		}
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
	}

	public function get_token($request, $wrap = true)
	{
	    if (!is_null($request))
		{
			$parameters = $request->getData();

			$request->setParameterStatus(array(
				'final' => array('key'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			$found = $request->read($request, false);

			if (count($found) == 0)
			{
				//might be more secure to obfuscate reasons for failure
				return \iriki\engine\response::error('Authentication key not found.', $wrap);
			}
			else
			{
				//pull out user_id and validity
				$key = $found[0];

				//fail if revoked or expired when not a perpetual key
				if ($key['status'] != true OR ($key['ttl'] != 0 AND time(NULL) >= $key['created'] + $key['ttl']))
				{
					return \iriki\engine\response::error('Authentication key revoked or expired.', $wrap);
				}
				//or generate token, supplying: authenticated, remember and user
				//check that no session token outlives its parent at creation
				else
				{
					$req = array();

					if ($key['ttl'] == 0)
					{
						//will live until revoked, proceed
					}
					else
					{
						$death = $key['created'] + $key['ttl'];
						$short_death = $key['created'] + IRIKI_SESSION_SHORT;
						$long_death = $key['created'] + IRIKI_SESSION_LONG;

						//check lifetime for both remembered and un-
						if ($parameters['remember'] == false AND $short_death > $death)
						{
							return \iriki\engine\response::error('Session would have lived longer than authentication key.', $wrap);
						}
						else if ($parameters['remember'] == true AND $long_death > $death)
						{
							return \iriki\engine\response::error('Session would have lived longer than authentication key.', $wrap);
						}
					}

					$req = array(
			            'code' => 200,
			            'message' => '',
			            'data' => array(
			                'model' => 'user_session',
			                'action' => 'initiate',
			                'url_parameters' => array(),
			                'params' => array(
			                    'authenticated' => true,
			                    'remember' => $parameters['remember'],
			                    'user_id' => $key['user_id']
			                )
			            )
			        );

			        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

			        $status = \iriki\engine\route::matchRequestToModel(
			            $GLOBALS['APP'],
			            $model_profile,
			            $req,
			            $request->getTestMode(),
			            $request->getSession()
			        );

			        return $status;
				}
			}
		}
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
	}

	public function read_by_user($request, $wrap = true)
	{
	    if (!is_null($request))
		{
			$request->setParameterStatus(array(
				'final' => array('user_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id')
			));

			return $request->read($request, $wrap);
		}
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
	}

    public function read_by_tag($request, $wrap = true)
    {
	    if (!is_null($request))
		{
			$request->setParameterStatus(array(
				'final' => array('tag'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			return $request->read($request, $wrap);
		}
	    else
	    {
	      return \iriki\engine\response::error('Null request.', $wrap);
	    }
    }
}

?>
