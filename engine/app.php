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

	public function initialise($request, $wrap = true)
	{
		return \iriki\engine\response::error('Not yet implemented.', $wrap);
	}

	public function reset($request, $wrap = true)
	{
		return \iriki\engine\response::error('Not yet implemented.', $wrap);
	}

	public function destroy($request, $wrap = true)
	{
		return \iriki\engine\response::error('Not yet implemented.', $wrap);
	}
}

?>