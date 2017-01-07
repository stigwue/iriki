<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
	const TYPE = 'mongodb';
	private static $__instance;

	public static function strToId($query)
	{
		//convert mongo id from string into the object
		foreach ($query as $key => $value)
		{
			if ($key == '_id')
			{
				$query[$key] = new \MongoId($value);
			}
		}

		return $query;
	}

	public static function initInstance()
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

	public static function doCreate($params_persist)
	{
		//params is table/collection and data to insert

		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			$persist = Self::$__instance->$params_persist['persist'];

			$params_persist['data']['created'] = time(NULL);

			$status = $persist->insert($params_persist['data']);

			return $status;
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
			$persist = Self::$__instance->$params_persist['persist'];

			//build query (key => value array)
			$query = $params_persist['data'];

			$query = Self::strToId($query);

			$cursor = $persist->find($query);

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
					$list[] = $object;
				}
				$status = $list;
			}

			return $status;
		}
	}

	public static function doUpdate($params_persist)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			$persist = Self::$__instance->$params_persist['persist'];

			//build query (key => value array)
			$query = array(
				'_id' => new \MongoId($params_persist['data']['_id'])
			);
			unset($params_persist['data']['_id']);

			$params_persist['data']['modified'] = time(NULL);

			$status = $persist->update($query, array('$set' => $params_persist['data']));

			return $status;
		}
	}

	public static function doDelete($params_persist)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			$persist = Self::$__instance->$params_persist['persist'];

			//build query (key => value array)
			$query = $params_persist['data'];

			$query = Self::strToId($query);

			$status = $persist->remove($query);

			return $status;
		}
	}
}

?>
