<?php

namespace iriki\engine;

require_once(__DIR__ . '/default.php');

class mysql extends database
{
	const TYPE = 'mysql';
	const ID_FIELD = 'id';

	private static $__instance = null;

	public static function initialize()
	{
		if (is_null(Self::$__instance))
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
	}

		public static function doCreate($request)
		{
			//params is table/collection and data to insert

			if (is_null(Self::$__instance))
			{
				return null;
			}
			else
			{
				\R::setAutoResolve(TRUE);

				$persist = \R::dispense($request->getModel());

				$data = $request->getData();

				//loop through data array
				//to build object->property = value
				foreach ($data as $property => $value)
				{
					$persist->$property = $value;
				}
				$persist->created = time(NULL);

				$status = \R::store($persist);

				//query only id
				$request->setData(array(Self::ID_FIELD => $status));

				//re-read it to return properties
				return Self::doRead($request);
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
				\R::setAutoResolve(TRUE);

				//build query (key => value array)
				/*
				array(
				*    'PROPERTY' => array( POSSIBLE VALUES... 'John', 'Steve' )
				*    'PROPERTY' => array( POSSIBLE VALUES... )
				* );
				*/

				$cursor = \R::find($request->getModel(), $request->getData());

				$status = array();

				if (count($cursor) == 0)
				{
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

		public static function doUpdate($request)
		{
			if (is_null(Self::$__instance))
			{
				return null;
			}
			else
			{
				\R::setAutoResolve(TRUE);

				$persist = \R::dispense($request->getModel());

				$data = $request->getData();

				//loop through data array
				//to build object->property = value
				foreach ($data as $property => $value)
				{
					$persist->$property = $value;
				}

				$id_field = $data[Self::ID_FIELD];

				$persist->modified = time(NULL);

				$status = \R::store($persist);

				//query only id
				$request->setData(array(Self::ID_FIELD => $status));

				//re-read it to return properties
				return Self::doRead($request);
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
				\R::setAutoResolve(TRUE);

				$cursor = \R::find($request->getModel(), $request->getData());

				$status = array();

				foreach ($cursor as $object) $status = \R::trash($object);

				return $status;
			}
		}
	}
	?>
