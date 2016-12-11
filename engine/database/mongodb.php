<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
	const TYPE = 'mongodb';
	private static $__instance;

	public static function initInstance()
	{
		//parse key values
		if (!is_null(Self::$_key_values))
		{
			$key_values = Self::$_key_values;
			if (
				$key_values['type'] == Self::TYPE AND
				isset($key_values['db'])
			)
			{
				Self::$__instance = new \MongoClient();

				Self::$__instance = Self::$__instance->$key_values['db'];
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
	public static function doCreate($params)
	{
		//params is table/collection and data to insert

		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			$persist = Self::$__instance->$params['__persist'];

			unset($params['__persist']);

			return $persist->insert($params);
		}
	}
}

?>