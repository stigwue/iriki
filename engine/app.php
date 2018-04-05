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

	public function models($request, $wrap = true)
	{
		$app = $GLOBALS['APP'];

		//get the models
		$models = $app['models'];

		//this gives us engine and app, join them up
		$model_list = array();
		foreach ($models['app'] as $app_model => $details)
		{
			$model_list[] = $app_model;
		}
		foreach ($models['engine'] as $engine_model => $details)
		{
			$model_list[] = $engine_model;
		}

        return \iriki\engine\response::data($model_list, $wrap);
	}

	public static function model_matrix($request, $wrap = true)
    {
		$app = $GLOBALS['APP'];

		//get the models
		$models = array_merge($app['models']['app'], $app['models']['engine']);

        $size = count($models);

        $matrix = array();

        //first loop
        foreach ($models as $current_model => $_model)
        {
            $matrix_row = array();
            //second loop for dependency ('belongsto') check
            foreach ($models as $_model_name => $_model_two)
            {
                //inner loop for 'belongsto' check
                if (in_array($current_model, $_model_two['relationships']['belongsto'], true))
                {
                    $matrix_row[] = 1;
                }
                else $matrix_row[] = 0;
            }
            $matrix[] = $matrix_row;
        }

        return \iriki\engine\response::data($matrix, $wrap);
    }
}

?>