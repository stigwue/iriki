<?php

namespace kronos;

/*
For collections, instancing is on two levels.
1. One can be referring to just the collection.
2. One would want to create an instance of its items.

Create a collection instance, have that as the parent
type: instance, parent: instance_id, value

One would need special code to store and retrieve collection items instances. As their items are the ones to be stored and/or retrieved.

There is also a risk of dangling instances if a collection_item has been removed from its collection after an instance has been created.
*/

class instance extends \iriki\engine\request
{
	//one shouldn't recurse more than 1 for a create
	//break it up into seperate requests from the most primitive upwards
	public function create($request, $wrap = true)
	{
		$parameters = $request->getData();

		if ($parameters['recursion'] == 0)
		{
			//no recursion whatsoever
			
			//drop the recursion parameter
			$request->setParameterStatus([
				'final' => array('type', 'parent', 'value'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			]);

			unset($parameters['recursion']);

			$request->setData($parameters);

			return $request->create($request, $wrap);
		}
		else
		{
			//recursion for collection types
			if ($parameters['type'] != 'collection')
			{
				return \iriki\engine\response::error('Recursive action not available for this type.');
			}
			else
			{
				//to expand this collection, for this instance:
				//type will be the item type
				//parent will be the collection_item _id
				//value is its value

				//but, from supplied parameters
				//type is collection
				//parent is the collection _id and
				//value will be a key-value dictionary:
				//key: collection_item _id
				//value: value for collection_item

				$collection_id = $parameters['parent'];
				$items = $parameters['value'];

				//item's read_by_collection will get us the items, should match the key-value dictionary of value
				$req = array(
		            'code' => 200,
		            'message' => '',
		            'data' => array(
		                'model' => 'collection_item',
		                'action' => 'read_by_collection',
		                'url_parameters' => array(),
		                'params' => array(
		            		'collection_id' => $collection_id
		                )
		            )
		        );

		        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

		        //handle the request: match a route to a model and its action
		        $status = \iriki\engine\route::matchRequestToModel(
		        	$GLOBALS['APP'],
		        	$model_profile,
		        	$req,
					$request->getTestMode() //test mode
		        );

		        if ($status['code'] == 200)
		        {
		        	$items_found = $status['data'];

		        	if (count($items_found) == 0 OR (count($items_found) != count($items)))
		        	{
		        		return \iriki\engine\response::error('Instance supplied does not match model records.');
		        	}
		        	else
		        	{
		        		//convert $items_found to a dictionary: item_id => item
		        		$dict_items_found = array();
		        		foreach ($items_found as $item_found)
		        		{
		        			if (!isset($dict_items_found[$item_found['_id']]))
		        			{
		        				$dict_items_found[$item_found['_id']] = $item_found;
		        			}
		        		}

		        		$instance_ids = array();

		        		//match and save individual items
		        		foreach ($items as $supplied_item_id => $supplied_item_value)
		        		{
		        			//is found test
		        			if (isset($dict_items_found[$supplied_item_id]))
		        			{
		        				//write the collection_item
		        				$coll_item = $dict_items_found[$supplied_item_id];

		        				$req_r = array(
						            'code' => 200,
						            'message' => '',
						            'data' => array(
						                'model' => 'instance',
						                'action' => 'create',
						                'url_parameters' => array(),
						                'params' => array(
						            		'type' => $coll_item['type'],
						                    'parent' => $coll_item['_id'],
						                    'value' => $supplied_item_value,
						                    'recursion' => 0
						                )
						            )
						        );

						        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req_r);

						        //handle the request: match a route to a model and its action
						        $status = \iriki\engine\route::matchRequestToModel(
						        	$GLOBALS['APP'],
						        	$model_profile,
						        	$req_r,
									$request->getTestMode() //test mode
						        );

						        if ($status['code'] == 200)
						        {
		        					$instance_ids[] = $status['data'];
						        }
		        			}
		        		}

		        		return \iriki\engine\response::data($instance_ids, $wrap);
		        	}
		        }
		        else
		        {
        			return \iriki\engine\response::error('Failed to read model records.');
		        }
			}
		}
	}

	public function read($request, $wrap = true)
	{
		$parameters = $request->getData();

		if ($parameters['recursion'] == 0)
		{
			//no recursion whatsoever
			
			//drop the recursion parameter
			$request->setParameterStatus([
				'final' => array('_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('_id')
			]);

			unset($parameters['recursion']);

			$request->setData($parameters);

			return $request->read($request, $wrap);
		}
		else
		{
			//recursion for collection types
			if ($parameters['type'] != 'collection')
			{
				return \iriki\engine\response::error('Recursive action not available for this type.');
			}
			else
			{
				//read a collection recursively
				//call this function again and again, reducing the recursion number
				//manage results
			}
		}
	}
}

?>