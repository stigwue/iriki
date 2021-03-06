<?php

namespace iriki;

class app extends \iriki\engine\request
{
	//app
	//engine
	//database
	//constants: upload_dir, base, base_url
	//routes: alias, default, list
	//models: list

	public function config($request, $wrap = true)
	{
		$app = $GLOBALS['APP'];
        return \iriki\engine\response::data($app, $wrap);
	}

	public function wheel($request, $wrap = true)
	{
		$result = array(
			'list' => array(),
			'matrix' => array()
		);

		$app = $GLOBALS['APP'];

		//get the models
		$models = $app['models'];

		//this gives us engine and app, join them up
		$model_list = array();
		foreach ($models['app'] as $app_model => $details)
		{
			$result['list'][] = $app_model;
		}
		foreach ($models['engine'] as $engine_model => $details)
		{
			$result['list'][] = $engine_model;
		}
		
		//merge the two model spaces
		$all_models = array_merge($models['app'], $models['engine']);

		//get the dependency matrix
		$size = count($all_models);

        //first loop
        foreach ($all_models as $current_model => $_model)
        {
            $matrix_row = array();
            //second loop for dependency ('belongsto') check
            foreach ($all_models as $_model_name => $_model_two)
            {
                //inner loop for 'belongsto' check
                if (is_null($_model_two['relationships']['belongsto']))
                {
                	if (in_array($current_model, array(), true))
	                {
	                    $matrix_row[] = 1;
	                }
                	else $matrix_row[] = 0;
                }
                else
                {
                	if (in_array($current_model, $_model_two['relationships']['belongsto'], true))
	                {
	                    $matrix_row[] = 1;
	                }
                	else $matrix_row[] = 0;
                }
            }
            $result['matrix'][] = $matrix_row;
        }

        return \iriki\engine\response::data($result, $wrap);
	}

	public function models($request, $wrap = true)
	{
		$app = $GLOBALS['APP'];

		//get the models
		$models = $app['models'];

		//try filling out model details?

		//merge the two model spaces
		$all_models = array_merge($models['app'], $models['engine']);

        return \iriki\engine\response::data($all_models, $wrap);
	}

	public function routes($request, $wrap = true)
	{
		$app = $GLOBALS['APP'];

		//get the routes
		$routes = $app['routes'];

		//routes/app and routes/engine all have the following:
		//default, alias, synonym, list and routes

		$defaults = array_merge($app['routes']['engine']['default'], $app['routes']['app']['default']);

		//merge the two model spaces
		$all_routes = array_merge($routes['app']['routes'], $routes['engine']['routes']);


		$result = array(
			'default' => $defaults,
			'custom' => array()
		);

		
		//expand custom route parameters. note that we would also include default routes if not overridden
		//get the models
		$models = $app['models'];
		//merge them
		$all_models = array_merge($models['app'], $models['engine']);
		
		//loop through existing routes
		foreach ($all_routes as $model => $route_details)
		{
			if (is_null($route_details)) continue;
			$expanded_routes = array();
			$model_details = $all_models[$model];

			//for each models routes....
			foreach ($route_details as $route => $details)
			{
				$deets = \iriki\engine\model::doExpandProperty($model_details, $details);

				$parameters = $deets['valid_properties'];

				$details['parameters'] = array_values($parameters);

				$expanded_routes[$route] = $details;
			}
			$result['custom'][$model] = $expanded_routes;

			//check also default routes to see if expanded
			foreach ($defaults as $route => $details)
			{
				if (!isset($route_details[$route]))
				{
					$deets_default = \iriki\engine\model::doExpandProperty($model_details, $details);

					$parameters = $deets_default['valid_properties'];

					$result['custom'][$model][$route] = $details;
					$result['custom'][$model][$route]['parameters'] = array_values($parameters);
				}
			}
		}

        return \iriki\engine\response::data($result, $wrap);
	}

}

?>