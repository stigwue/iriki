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
                if (in_array($current_model, $_model_two['relationships']['belongsto'], true))
                {
                    $matrix_row[] = 1;
                }
                else $matrix_row[] = 0;
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
			'custom' => $all_routes
		);

        return \iriki\engine\response::data($result, $wrap);
	}

}

?>