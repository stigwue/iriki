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
    * @param request_object Request object
    * @return Response
    * @throw
    */
    public static function doCreate($request_obj)
	{
		return response::error('Action not yet configured');
	}

	/**
    * Perform a database read action on a request.
    *
    * @param request_object Request object
    * @param meta Request metadata such as sort, limit etc
    * @return Response
    * @throw
    */
    public static function doRead($request_obj, $meta)
	{
		return response::error('Action not yet configured');
	}

	/**
    * Perform a database update action on a request.
    *
    * @param request_object Request object
    * @return Response
    * @throw
    */
    public static function doUpdate($request_obj)
	{
		return response::error('Action not yet configured');
	}

	/**
    * Perform a database delete action on a request.
    *
    * @param request_object Request object
    * @return Response
    * @throw
    */
    public static function doDelete($request_obj)
	{
		return response::error('Action not yet configured');
	}
}

?>
