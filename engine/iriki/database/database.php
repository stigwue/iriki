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
    * Perform a database create action on a request.
    *
    * @param object Request object
    * @returns object Response
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
    * @returns object Response
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
    * @returns object Response
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
    * @returns object Response
    * @throw
    */
    public static function doDelete($request_obj)
	{
		return response::error('Action not yet configured');
	}
}

?>
