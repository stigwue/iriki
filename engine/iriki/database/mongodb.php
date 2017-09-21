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
    * Database engine identifier.
    * This is unique across the framework.
    *
    * @var {string}
    */
	const TYPE = 'mongodb';

	/**
    * Database id field.
    * Any property/column with this name is an id.
    * Parent model ids are also built with this.
    *
    * @var {string}
    */
	const ID_FIELD = '_id';

	/**
    * Internal database handle.
    * This is shared across all instances of this class.
    * So handle carefully.
    *
    * @var {Object}
    */
	private static $__instance = null;

	/**
    * Checker for valid Mongo IDs.
    *
    * @param {string} The id to check.
    * @return {boolean} True or false
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
    * @return {boolean} True or false
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
				//use IRIKI_REFRESH to calculate
				$expire_stamp = $user_session['created'] + IRIKI_REFRESH;
				if ($timestamp >= $expire_stamp)
				{
					//expired
					//var_dump('expired', $timestamp, $expire_stamp);
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
    * @return {Array} Corrected query array
    */
	public static function enforceIds($parameters, $key_values)
	{
		//convert mongo id from string into the object
		$query = array();

		foreach ($parameters['final'] as $index => $key)
		{
			$id_key = array_search($key, $parameters['ids']);
			if ($key == Self::ID_FIELD OR $id_key !== FALSE)
			{
				if (Self::isMongoId($key_values[$key]))
				{
					//$key is an id, enforce
					$query[$key] = new \MongoId($key_values[$key]);
				}
				else
				{
					//value isn't a valid MongoId
					//skip, still use or default?
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
    * @return {Array} Corrected array
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
    * Intialize internal database handle using supplied configuration
    *
    * @return {boolean} Success value of operation.
    */
	public static function initialize()
	{
		if (is_null(Self::$__instance))
		{
			if (is_null(Self::$_key_values))
			{
				return false;
			}
			else
			{
				//parse key values
				$key_values = Self::$_key_values;
				if (
					$key_values['type'] == Self::TYPE AND
					isset($key_values['server']) AND
					isset($key_values['db'])
				)
				{
					Self::$__instance = new \MongoClient($key_values['server']);

					Self::$__instance = Self::$__instance->$key_values['db'];

					return true;
				}
				else
				{
					return false;
				}
		  }
		}
	}

	/**
    * Database create action.
    *
    * @param {request} Request on which action is performed
    * @return {Array} One of three options: null, an array with message (true or false) and data values or an array with code (some error code) and message (string description)
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
						'code' => '401', //response::AUTH
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
    * @return {Array} One of three options: null, an array of data values or an array with code (some error code) and message (string description)
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
						'code' => '401',
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
    * @return {Array} One of three options: null, a success flag or an array with code (some error code) and message (string description)
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
						'code' => '401',
						'message' => 'unauthorized'
					);
				}
			}

			$collection = $request->getModel();
			$persist = Self::$__instance->$collection;

			$data = $request->getData();
			$params = $request->getParameterStatus();

			$data = Self::enforceIds($params, $data);

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
    * @return {Array} One of three options: null, a success flag or an array with code (some error code) and message (string description)
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
						'code' => '401',
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

			$status = $persist->remove($query);

			$status_flag = ($status["n"] != 0);

			return $status_flag;
		}
	}
}

?>
