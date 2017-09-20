<?php

namespace iriki\engine;

/**
 * Iriki database engine template.
 * There should be an extension (inheritance) of this
 * for every supported database.
 *
 */
class database
{
	/**
    * Associative array of database parameters.
    *
    * @var {Array}
    */
    protected static $_key_values;
	
	/**
    * Database type identifier, unique through out the framework.
    *
    * @var string
    */
    protected static $_type = 'default';
	
	/**
    * Database class full namespace.
    *
    * @var string
    */
    protected static $_namespace;

	/**
    * Database class, typically namespace and type.
    *
    * @var string
    */
    protected static $_db_class;
	
	/**
    * Database class exists in code.
    *
    * @var boolean
    */
    protected static $_db_class_exists;
	
	/**
    * Database class instance.
    *
    * @var Object
    */
    protected static $_instance;

    /**
    * Initialize the database instance
    *
    * @param {string} $app The application name
    * @param {string} $engine The application engine
    * @param {Array} $config_values Application configuration values
    * @returns {boolean} True or false class exists value.
    * @throw
    */
	public static function doInitialise($app, $engine = 'iriki', $config_values = null)
	{
		Self::$_key_values = $config_values;


		if (isset(Self::$_key_values['type']))
		{
			Self::$_type = Self::$_key_values['type'];
		}
		else
		{
			//a db wasn't specified. no persistence then?
			Self::$_type = '';
			Self::$_instance = null;
			Self::$_db_class_exists = false;
		}

		//defined in \iriki\engine
		//or \app\database
		Self::$_namespace = '\\' . $engine . '\\engine\\';
		Self::$_db_class = Self::$_namespace . Self::$_type;
		if (class_exists(Self::$_db_class))
		{
			Self::$_instance = new Self::$_db_class();
			Self::$_db_class_exists = true;
		}
		else
		{
			//try application
			Self::$_namespace = '\\' . $app . '\\engine\\';
			Self::$_db_class = Self::$_namespace . Self::$_type;
			if (class_exists(Self::$_db_class))
			{
				Self::$_instance = new Self::$_db_class();
				Self::$_db_class_exists = true;
			}
		}
		return Self::$_db_class_exists;
	}

	/**
    * Gets the database type.
    *
    *
    * @return string Database type
    * @throw
    */
    public static function getType()
	{
		return Self::$_type;
	}

	/**
    * Gets the database class.
    *
    *
    * @return string Database class
    * @throw
    */
	public static function getClass()
	{
		return Self::$_db_class;
	}

	/**
    * Perform a database create action on a request.
    *
    * @param object Request object
    * @return object Response
    * @throw
    */
    public static function doCreate($request_obj)
	{
		return response::error('Action not yet configured');
	}

	/**
    * Perform a database read action on a request.
    *
    * @param object Request object
    * @param array Request data sort
    * @return object Response
    * @throw
    */
    public static function doRead($request_obj, $sort)
	{
		return response::error('Action not yet configured');
	}

	/**
    * Perform a database update action on a request.
    *
    * @param object Request object
    * @return object Response
    * @throw
    */
    public static function doUpdate($request_obj)
	{
		return response::error('Action not yet configured');
	}

	/**
    * Perform a database delete action on a request.
    *
    * @param object Request object
    * @return object Response
    * @throw
    */
    public static function doDelete($request_obj)
	{
		return response::error('Action not yet configured');
	}
}

?>
