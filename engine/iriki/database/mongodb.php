<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
	const TYPE = 'mongodb';
	const ID_FIELD = '_id';

	private static $__instance = null;

	//for (de)enforce, note http://php.net/manual/en/mongoid.isvalid.php
	//deprecation notice
	//http://stackoverflow.com/a/36135790/3323338
	private static function isMongoId($id){
		return is_string($id) && strlen($id) == 24 && ctype_xdigit($id);
	}

	//parameters has final, missing, extra and ids
	//we check final for ID_FIELD and set
	//we check ids and enforce all
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

	//function to de-enforceIds
	//if a mongo_id was x_id, it would be array('$id' => 'string representation')
	//we need to fix this here, i've seen the alternative and it aint pretty
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

	public static function doCreate($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
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

	public static function doRead($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
			$collection = $request->getModel();
			$persist = Self::$__instance->$collection;

			//build query (key => value array)
			$query = $request->getData();
			$params = $request->getParameterStatus();

			$query = Self::enforceIds($params, $query);

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
					$list[] = Self::deenforceIds($object);
				}
				$status = $list;
			}

			return $status;
		}
	}

	public static function doUpdate($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
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

	public static function doDelete($request)
	{
		if (is_null(Self::$__instance))
		{
			return null;
		}
		else
		{
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
