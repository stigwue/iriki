<?php

namespace iriki\engine;

//get the definition of the default database class
require_once(__DIR__ . '/database.php');

//response
require_once(__DIR__ . '/../response.php');

/**
* Iriki MongoDB database engine.
*
*/
class mongodb extends database
{
	/**
    * Database id field.
    * Any property/column with this name is an id.
    * Parent model ids are also built with this.
    *
    */
	const ID_FIELD = '_id';
	
	/**
    * Default connection port.
    *
    */
	const PORT = 27017;

	/**
    * Associative array of database parameters.
    *
    */
    private static $_key_values;

	/**
    * Internal database handle.
    * This is shared across all instances of this class.
    * So handle carefully.
    *
    */
	private static $__instance = null;

	/**
    * Gets the database class.
    *
    *
    * @return Database class
    * @throw
    */
	public static function getClass()
	{
		return static::class;
	}

	/**
    * Gets the internal database instance.
    *
    *
    * @return Mongo intance
    */
	public static function getInstance()
	{
		return Self::$__instance;
	}

	/**
    * Build the connection string, taking into account server and authentication details
    *
    * @param properties Database configuration values
    * @return Connection string.
    * @throw
    */
	public static function buildConnString($properties)
	{
		//two modes of access exist, default and custom
		//default: server (mongodb://server:port) and db alone supplied
		//custom: port, user & password suppled additionally

		$conn_string = '';

		if (substr($properties['server'], 0, strlen('mongodb://')) == 'mongodb://')
		{
			//use supplied string
			$conn_string = $properties['server'];
		}
		else
		{
			//custom build string

			//is port supplied? no? then server has all we need
			//is user, password supplied? then use supplied or default port

			$port = (isset($properties['port']) ? $properties['port'] : Self::PORT);

			
			// mongodb://${user}:${pwd}@server:port
			if (isset($properties['user']) AND isset($properties['password']))
			{
				//authentication needed
				$conn_string = 'mongodb://' . $properties['user'] . ':' . $properties['password'] . '@' . $properties['server'] . ':' . $port;
			}
			else
			{
				//no authentication needed
				$conn_string = 'mongodb://' . $properties['server'] . ':' . $port;
			}
		}

		return $conn_string;
	}

    /**
    * Initialize the database instance using supplied configuration
    *
    * @param config_values Database configuration values
    * @return True or false value.
    * @throw
    */
	public static function doInitialise($config_values)
	{
		//we have supplied the db's properties in config
		//the engine name
		//and app name

		//the trick is to see if config values are null
		//if they aren't, we find which class exists (using app first)
		//we then use the engine as last resort

		//we have config values
		if (is_null($config_values))
		{
			return null;
		}
		else
		{
			//make sure we have our needed parameters
			Self::$_key_values = $config_values;
			if (
				//class must exist to handle the type
				class_exists(Self::$_key_values['type']) AND
				//class must be this one
				Self::$_key_values['type'] == '\\' . Self::getClass() AND
				isset(Self::$_key_values['server']) AND
				isset(Self::$_key_values['db'])
			)
			{
			    $mongo_db = Self::$_key_values['db'];

				Self::$__instance = (new \MongoDB\Client(Self::buildConnString(Self::$_key_values)))->$mongo_db;

				return Self::$__instance;
			}
			else
			{
				return null;
			}
		}
	}

	/**
    * Destroy the database instance
    *
    * @return True or false value.
    * @throw
    */
	public static function doDestroy()
	{
		//close all then
		Self::$__instance = null;

		return is_null(Self::$__instance);
	}

	/**
    * Checker for valid Mongo IDs.
    *
    * @param id The id to check.
    * @return True or false
    */
	public static function isMongoId($id){
		return is_string($id) && strlen($id) == 24 && ctype_xdigit($id);
	}

	/**
    * Check database if user supplied session token is valid.
    * Returns true if all is well or false otherwise so that
    * calling function can
    * return response::error('User session token invalid or expired.');
    *
    * @param user_session_token Session token.
    * @param timestamp Timestamp to use for expiry checks
    * @return True or false
    */
    private static function checkSessionToken($user_session_token, $timestamp, $auth_details, $orig_request)
	{
		//read session details from db
		$persist = Self::$__instance->user_session;

		//build query (key => value array)
		$query = array(
			'token' => $user_session_token
		);

		$cursor = $persist->find($query);


		//loop through cursor
		//cursor should hold only one session object
		foreach ($cursor as $user_session)
		{
			//is it authenticated?
			if ($user_session['authenticated'] == false) {
				return false;
			}

			//does its user still exist?
			//err, beyond our scope, ignore

			//has it expired? depends on if it is to be remembered
			$expire_stamp = $user_session['pinged'];
			if ($user_session['remember'] == true)
			{
				$expire_stamp = $user_session['pinged'] + IRIKI_SESSION_LONG;
			}
			else
			{
				$expire_stamp = $user_session['pinged'] + IRIKI_SESSION_SHORT;
			}
			if ($timestamp >= $expire_stamp)
			{
				//expired
				return false;
			}
			else
			{
				//IP check?
				//too stringent, ignore for now
				//$ip = $_SERVER['SERVER_ADDR'];
				//if ($ip == $user_session['ip'])

				//perform other checks
				//note that we are not specific on why auth failed

				//1. if user_authenticate is true, is provided session token that of the user?
				if ($auth_details['user'] == true)
				{
					if ($auth_details['user_authorized'] != $user_session['user_id'])
					{
						return false;
					}
				}

				//2. if group to authenticate isn't empty, is the supplied user of the group(s)?
				if (count($auth_details['group']) != 0)
				{
					//run user_access/user_in_any_group_special
					$request = array(
			            'code' => 200,
			            'message' => '',
			            'data' => array(
			                'model' => 'user_access',
			                'action' => 'user_in_any_group_special',
			                'url_parameters' => array(),
			                'params' => array(
			                    'user_id' => $user_session['user_id'],
			                    'title_array' => [
			                        $auth_details['group']
			                    ]
			                )
			            )
			        );

			        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

			        $status = \iriki\engine\route::matchRequestToModel(
			            $GLOBALS['APP'],
			            $model_profile,
			            $request,
			            $orig_request->getTestMode()
			        );

					if ($status['code'] == 200)
					{
						if($status['message'] != true)
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}

				//3. if group to NOT authenticate isn't empty,... 
				if (count($auth_details['group_not']) != 0)
				{
					//run user_access/user_in_any_group_special
					$request = array(
			            'code' => 200,
			            'message' => '',
			            'data' => array(
			                'model' => 'user_access',
			                'action' => 'user_in_any_group_special',
			                'url_parameters' => array(),
			                'params' => array(
			                    'user_id' => $user_session['user_id'],
			                    'title_array' => [
			                        $auth_details['group_not']
			                    ]
			                )
			            )
			        );

			        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

			        $status = \iriki\engine\route::matchRequestToModel(
			            $GLOBALS['APP'],
			            $model_profile,
			            $request,
			            $orig_request->getTestMode()
			        );

					if ($status['code'] == 200)
					{
						if($status['message'] == true)
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}

				return true;
			}
		}

		//token not found
		return false;
	}

	/**
    * Convert IDs supplied as strings to MongoIDs
    * Invalid IDs will be added to missing parameters
    *
    * @param parameters Parameters: final, missing and ids.
    * @param key_values Parameter values
    * @return Corrected query array
    */
	public static function enforceIds(&$parameters, $key_values)
	{
		//convert mongo id from string into the object
		$query = array();

		foreach ($parameters['final'] as $index => $key)
		{
			$id_key = array_search($key, $parameters['ids']);
			if ($key == Self::ID_FIELD OR $id_key !== FALSE)
			{
				if (isset($key_values[$key]) && Self::isMongoId($key_values[$key]))
				{
					//$key is an id, enforce
					$query[$key] = new \MongoDB\BSON\ObjectId($key_values[$key]);
				}
				else
				{
					//this parameter's value is:
					//not a valid MongoId
					//or has not been set/provided

					//we should indicate it as missing
					//so as to alert the next step in the process
					//to truncate things
					$parameters['missing'][] = $key;
					continue;
				}
			}
			else
			{
				$query[$key] = $key_values[$key];
			}
		}

		return $query;
	}

	/**
    * Because MongoIDs are complex variables (an array like '$id' => 'string representation'),
    * we need to pull out only the string value.
    *
    * @param key_values Associative array of key and values
    * @return Corrected array
    */
	public static function deenforceIds($key_values)
	{
		$pretty = array();

		foreach ($key_values as $key => $value)
		{
			if (
				$key == Self::ID_FIELD OR
				Self::isMongoId((string) $value)
			)
			{
				$pretty[$key] = (string) $value;
			}
			else
			{
				$pretty[$key] = $value;
			}
		}

		return $pretty;
	}

	/**
    * Reduce complex MongoIDs to string.
    *
    * @param id MongoDB BSON object
    * @return String representation
    */
	public static function deenforceId($id)
	{
		if (Self::isMongoId((string) $id))
		{
			return (string) $id;
		}
		else
		{
			return $id;
		}
	}

	/**
    * Database create action.
    *
    * @param request Request on which action is performed
    * @return One of three options: null, an array with message (true or false) and data values or an array with code (some error code) and message (string description)
    */
	public static function doCreate($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			if (!is_null($request->getSession()))
			{
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL), $request->getAuthentication(), $request);

				if (!$authenticated)
				{
					return array(
						'code' => \iriki\engine\response::AUTH,
						'message' => 'unauthorized'
					);
				}
			}

			$collection = $request->getModel();
			$persist = Self::$__instance->$collection;

			//build query (key => value array)
			$query = $request->getData();
			$params = $request->getParameterStatus();

			$query = Self::enforceIds($params, $query);
			//check if $params['missing'] has increased
			if (count($params['missing']) != 0)
			{
				return array(
					'code' => \iriki\engine\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			$query[Self::ID_FIELD] = new \MongoDB\BSON\ObjectId();
			$query['created'] = time(NULL);

      		$status = $persist->insertOne($query);

			return array(
				'message' => $status->isAcknowledged(),
				'data' => (string) $status->getInsertedId()
			);
		}
	}

	/**
    * Database read action.
    *
    * @param request Request on which action is performed
    * @param meta Array to control read action e.g sort, limit etc
    * @return One of three options: null, an array of data values or an array with code (some error code) and message (string description)
    */
	public static function doRead($request, $meta)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			if (!is_null($request->getSession()))
			{
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL), $request->getAuthentication(), $request);

				if (!$authenticated)
				{
					//return this array to signal to calling function
					//that request is unauthorized
					//we could have done
					//return \iriki\engine\response::auth('User session token invalid or expired.');
					//but it would result in double wrapping
					return array(
						'code' => \iriki\engine\response::AUTH,
						'message' => 'unauthorized'
					);
				}
			}

			$collection = $request->getModel();
			$persist = Self::$__instance->$collection;

			//build query (key => value array)
			$query = $request->getData();
			$params = $request->getParameterStatus();

			$query = Self::enforceIds($params, $query);
			//check if $params['missing'] has increased
			if (count($params['missing']) != 0)
			{
				return array(
					'code' => \iriki\engine\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			$count = (isset($meta['count']) ? $meta['count'] : false);

			if ($count)
			{
				return $persist->count($query);
			}
			else
			{
				$query_options = array();

				$sort = (isset($meta['sort']) ? $meta['sort'] : array());
				$limit = (isset($meta['limit']) ? $meta['limit'] : 0);

				if (count($sort) != 0)
				{
					$query_options['sort'] = $sort;
				}

				if ($limit != 0)
				{
					$query_options['limit'] = (int) $limit;
				}

				$cursor = null;

				if (count($query_options) != 0)
				{
					$cursor = $persist->find($query, $query_options);
				}
				else
				{
					$cursor = $persist->find($query);
				}

				$status = array();

				//loop through cursor
				$list = array();
				foreach ($cursor as $object)
				{
					$list[] = Self::deenforceIds($object);
				}
				$status = $list;

				return $status;
			}
		}
	}

	/**
    * Database update action.
    *
    * @param request Request on which action is performed
    * @return One of three options: null, a success flag or an array with code (some error code) and message (string description)
    */
	public static function doUpdate($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			if (!is_null($request->getSession()))
			{
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL), $request->getAuthentication(), $request);

				if (!$authenticated)
				{
					return array(
						'code' => \iriki\engine\response::AUTH,
						'message' => 'unauthorized'
					);
				}
			}

			$collection = $request->getModel();
			$persist = Self::$__instance->$collection;

			$data = $request->getData();
			$params = $request->getParameterStatus();

			$data = Self::enforceIds($params, $data);
			//check if $params['missing'] has increased
			if (count($params['missing']) != 0)
			{
				return array(
					'code' => \iriki\engine\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			//pick only the id field to be used to filter update
			$query = array(
				Self::ID_FIELD => new \MongoDB\BSON\ObjectId($data[Self::ID_FIELD])
			);

			//remove the id field to prevent it from being edited
			$id_field = $data[Self::ID_FIELD];
			unset($data[Self::ID_FIELD]);

			$data['modified'] = time(NULL);

			$status = $persist->updateOne($query, array('$set' => $data));

			$status_flag = ($status->getMatchedCount() == $status->getModifiedCount());

			return $status_flag;
		}
	}

	/**
    * Database delete action.
    *
    * @param request Request on which action is performed
    * @return One of three options: null, a success flag or an array with code (some error code) and message (string description)
    */
	public static function doDelete($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			if (!is_null($request->getSession()))
			{
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL), $request->getAuthentication(), $request);

				if (!$authenticated)
				{
					return array(
						'code' => \iriki\engine\response::AUTH,
						'message' => 'unauthorized'
					);
				}
			}

			$collection = $request->getModel();
			$persist = Self::$__instance->$collection;

			//build query (key => value array)
			$query = $request->getData();
			$params = $request->getParameterStatus();

			$query = Self::enforceIds($params, $query);
			//check if $params['missing'] has increased
			if (count($params['missing']) != 0)
			{
				return array(
					'code' => \iriki\engine\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			$status = $persist->deleteOne($query);

			$status_flag = ($status->getDeletedCount() != 0);

			return $status_flag;
		}
	}
}

?>
