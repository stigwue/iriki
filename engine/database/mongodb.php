<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
	const TYPE = 'mongodb';

	public function getInstance()
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
				$instance = new \MongoClient();
		        return $instance->$key_values['db'];
			}
			else
			{
				return null;
			}
	    }
		else
		{
			return null;
		}
	}
}

?>