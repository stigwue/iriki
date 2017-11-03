<?php

namespace iriki\engine;

//get the definition of the default database class
require_once(__DIR__ . '/database.php');

//response
require_once(__DIR__ . '/../response.php');

/**
* Iriki database engine.
* This is the mongodb instance.
*
*/
class mongodb extends database
{
	/**
    * Database id field.
    * Any property/column with this name is an id.
    * Parent model ids are also built with this.
    *
    * @var {string}
    */
	const ID_FIELD = '_id';

	/**
    * Associative array of database parameters.
    *
    * @var {Array}
    */
    private static $_key_values;

	/**
    * Internal database handle.
    * This is shared across all instances of this class.
    * So handle carefully.
    *
    * @var {Object}
    */
	private static $__instance = null;

	/**
    * Gets the database class.
    *
    *
    * @returns string Database class
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
    * @returns {Object} Mongo intance
    */
	public static function getInstance()
	{
		return Self::$__instance;
	}

    /**
    * Initialize the database instance using supplied configuration
    *
    * @param {Array} $config_values Database configuration values
    * @returns {boolean} True or false value.
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
				$mongo_client = new \MongoClient(Self::$_key_values['server']);
				$mongo_db = Self::$_key_values['db'];

				Self::$__instance = $mongo_client->$mongo_db;

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
    * @returns {boolean} True or false value.
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
    * @param {string} The id to check.
    * @returns {boolean} True or false
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
    * @param {string} Session token.
    * @param {integer} Timestamp to use for expiry checks
    * @returns {boolean} True or false
    */
    private static function checkSessionToken($user_session_token, $timestamp)
	{
		//read session details from db
		$persist = Self::$__instance->user_session;

		//build query (key => value array)
		$query = array(
			'token' => $user_session_token
		);

		$cursor = $persist->find($query);


		if (count($cursor) != 0)
		{
			//loop through cursor
			//cursor should hold only one session object
			foreach ($cursor as $user_session)
			{
				//is it authenticated? 
				if ($user_session['authenticated'] == 'false') {
					return false;
				}

				//does its user still exist?
				//err, beyond our scope, ignore

				//is it to be remembered?
				//ignore for now

				//has it expired?
				$expire_stamp = $user_session['pinged'];
				if ($user_session['remember'] == 'true')
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
					
					return true;
				}
			}
		}

		//token not found
		return false;
	}

	/**
    * Convert IDs supplied as strings to MongoIDs
    * Invalid IDs will be added to missing parameters
    *
    * @param {array} Parameters: final, missing and ids.
    * @param {array} Parameter values
    * @returns {Array} Corrected query array
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
					$query[$key] = new \MongoId($key_values[$key]);
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
    * @param {array} Associative array of key and values
    * @returns {Array} Corrected array
    */
	public static function deenforceIds($key_values)
	{
		$pretty = array();

		foreach ($key_values as $key => $value)
		{
			if (
				$key == Self::ID_FIELD OR
				isset($value->{'$id'})
			)
			{
				$pretty[$key] = $value->{'$id'};
			}
			else
			{
				$pretty[$key] = $value;
			}
		}

		return $pretty;
	}

	/**
    * Database create action.
    *
    * @param {request} Request on which action is performed
    * @returns {Array} One of three options: null, an array with message (true or false) and data values or an array with code (some error code) and message (string description)
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
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL));

				if (!$authenticated)
				{
					return array(
						'code' => \iriki\response::AUTH,
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
					'code' => \iriki\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			$query[Self::ID_FIELD] = new \MongoId();
			$query['created'] = time(NULL);

      		$status = $persist->insert($query);

			$status_flag = ($status["n"] == 0);

			return array(
				'message' => $status_flag,
				'data' => $query[Self::ID_FIELD]->{'$id'}
			);
		}
	}

	/**
    * Database read action.
    *
    * @param {request} Request on which action is performed
    * @param {array} Array to control read sort
    * @returns {Array} One of three options: null, an array of data values or an array with code (some error code) and message (string description)
    */
	public static function doRead($request, $sort)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			if (!is_null($request->getSession()))
			{
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL));

				if (!$authenticated)
				{
					//return this array to signal to calling function
					//that request is unauthorized
					//we could have done
					//return \iriki\response::auth('User session token invalid or expired.');
					//but it would result in double wrapping
					return array(
						'code' => \iriki\response::AUTH,
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
					'code' => \iriki\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			$cursor = $persist->find($query);

			if (count($sort) != 0)
			{
				$cursor->sort($sort);
			}

			$status = array();

			if (count($cursor) == 0)
			{
				$status = array();
			}
			else
			{
				//loop through cursor
				$list = array();
				foreach ($cursor as $object)
				{
					$list[] = Self::deenforceIds($object);
				}
				$status = $list;
			}

			return $status;
		}
	}

	/**
    * Database update action.
    *
    * @param {request} Request on which action is performed
    * @returns {Array} One of three options: null, a success flag or an array with code (some error code) and message (string description)
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
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL));

				if (!$authenticated)
				{
					return array(
						'code' => \iriki\response::AUTH,
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
					'code' => \iriki\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			//pick only the id field to be used to filter update
			$query = array(
				Self::ID_FIELD => new \MongoId($data[Self::ID_FIELD])
			);

			//remove the id field to prevent it from being edited
			$id_field = $data[Self::ID_FIELD];
			unset($data[Self::ID_FIELD]);

			$data['modified'] = time(NULL);

			$status = $persist->update($query, array('$set' => $data));

			$status_flag = $status["updatedExisting"];

			return $status_flag;
		}
	}

	/**
    * Database delete action.
    *
    * @param {request} Request on which action is performed
    * @returns {Array} One of three options: null, a success flag or an array with code (some error code) and message (string description)
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
				$authenticated = Self::checkSessionToken($request->getSession(), time(NULL));

				if (!$authenticated)
				{
					return array(
						'code' => \iriki\response::AUTH,
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
					'code' => \iriki\response::ERROR,
					'message' => 'missing_parameter'
				);
			}

			$status = $persist->remove($query);

			$status_flag = ($status["n"] != 0);

			return $status_flag;
		}
	}
}

?>
