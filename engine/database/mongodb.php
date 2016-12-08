<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
	const TYPE = 'mongodb';
	private static $__instance;

	private static function initInstance()
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
}

?>