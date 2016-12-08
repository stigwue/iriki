<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

//vendor - readbean
require_once(__DIR__ . '/default.php');

class mysql extends database
{
	const TYPE = 'mysql';

	public function getInstance()
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

				\R::setup("mysql:host=$server;dbname=$database", $key_values['user'], $password);

				return $this;
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