<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

//vendor - readbean
require_once(__DIR__ . '/default.php');

class mysql extends database
{
	const TYPE = 'mysql';
	private static $__instance;

	public static function initInstance()
	{
		//parse key values
		if (!is_null(Self::$_key_values))
		{
			$key_values = Self::$_key_values;
			if (
				$key_values['type'] == Self::TYPE
				//AND isset($key_values['server'])
				AND isset($key_values['db'])
				AND isset($key_values['user'])
				//AND isset($key_values['password'])
			)
			{
				$server = isset($key_values['server']) ? $key_values['server'] : 'localhost';
				$database = $key_values['db'];
				$password = isset($key_values['password']) ? $key_values['password'] : '';

				Self::$__instance = \R::setup("mysql:host=$server;dbname=$database", $key_values['user'], $password);

				return true;
			}
			else
			{
				return false;
			}
	    }
		else
		{
			return false;
		}
	}

	public static function doCreate($params_persist)
	{
		//params is table/collection and data to insert

		if (is_null(Self::$__instance))
		{
			return null;
			/*$status['error'] = array(
                'code' => 404,
                'message' => $model_status['details']['description']
            );*/
		}
		else
		{
			\R::setAutoResolve(TRUE);

			$persist = \R::dispense($params_persist['persist']);

			//loop through data array
			//to build object->property = value
			foreach ($params_persist['data'] as $property => $value)
			{
				$persist->$property = $value;
			}

			$status = \R::store($persist);

			return $status; //or some condition test
		}
	}

	public static function doRead($params_persist)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			\R::setAutoResolve(TRUE);

			//build query (key => value array)
			/*
			array(
			 *    'PROPERTY' => array( POSSIBLE VALUES... 'John', 'Steve' )
			 *    'PROPERTY' => array( POSSIBLE VALUES... )
			 * );
			 */

			$cursor = \R::find($params_persist['persist'], $params_persist['data']);

			$status = array();

			if (count($cursor) == 0)
			{
				$status['data'] = array();
			}
			else
			{
				//loop through cursor
				$list = array();
				foreach ($cursor as $object)
				{
					$list[] = $object;
				}
				$status['data'] = $list;
			}

			return $status;
		}
	}

	public static function doUpdate($params)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			\R::setAutoResolve(TRUE);

			$persist = \R::dispense($params_persist['persist']);

			//loop through data array
			//to build object->property = value
			foreach ($params_persist['data'] as $property => $value)
			{
				$persist->$property = $value;
			}

			$status = \R::store($persist);

			return $status; //or some condition test
		}
	}

	public static function doDelete($params)
	{
		//$status = \R::trash($persist);
	}
}
?>