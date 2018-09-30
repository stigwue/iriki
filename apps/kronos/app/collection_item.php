<?php

namespace kronos;

class collection_item extends \iriki\engine\request
{
	public function add($request, $wrap = true)
	{
		$parameters = $request->getData();

		//test exists first
		$req = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection_item',
                'action' => 'exists',
                'url_parameters' => array(),
                'params' => array(
            		'collection_id' => $parameters['collection_id'],
            		'model' => $parameters['model']
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

        if ($status['code'] == 200 && $status['data'] == false)
        {
        	//does not exist
        	$request->setParameterStatus([
				'final' => array('collection_id', 'type', 'model'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('collection_id'),
			]);

			return $request->create($request, $wrap);
        }
        else
		{
			return \iriki\engine\response::error('Item already exists in this collection.', $wrap);
		}
	}

	public function add_many($request, $wrap = true)
	{
		$parameters = $request->getData();

		$types = $parameters['type'];
		$models = $parameters['model'];

		if (
			(count($types) == count($models)) AND
			(count($types) != 0)
		)
		{
			$collection_id = $parameters['collection_id'];

			$item_ids = array();

			foreach ($types as $index => $type)
			{
				$req = array(
		            'code' => 200,
		            'message' => '',
		            'data' => array(
		                'model' => 'collection_item',
		                'action' => 'add',
		                'url_parameters' => array(),
		                'params' => array(
		            		'collection_id' => $collection_id,
		            		'type' => $type,
		            		'model' => $models[$index]
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
		        	$item_ids[] = $status['data'];
		        }
			}

	        return \iriki\engine\response::data($item_ids, $wrap);
		}
		else
		{
			return \iriki\engine\response::error('Parameter arrays do not match or are empty.', $wrap);
		}
		
	}

	public function read_by_collection($request, $wrap = true)
	{
		$request->setParameterStatus([
			'final' => array('collection_id'),
			'missing' => array(),
			'extra' => array(),
			'ids' => array('collection_id'),
		]);

		return $request->read($request, $wrap);
	}

	public function exists($request, $wrap = true)
	{
		$request->setParameterStatus([
			'final' => array('collection_id', 'model'),
			'missing' => array(),
			'extra' => array(),
			'ids' => array('collection_id'),
		]);

		$data = $request->read($request, false);
		$exists = (count($data) != 0);

		return \iriki\engine\response::data($exists, $wrap);
	}

	public function remove($request, $wrap = true)
	{
		$request->setParameterStatus([
			'final' => array('collection_id', 'model'),
			'missing' => array(),
			'extra' => array(),
			'ids' => array('collection_id'),
		]);

		return $request->delete($request, $wrap);
	}
}

?>