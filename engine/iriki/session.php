<?php

namespace iriki;

class session extends engine\model
{
	public function create($db_type, $params = null)
	{
		$instance = new $db_type();
		$instance::initInstance();

		//var_dump($params);

		if (!is_null($params))
		{
			$params['__persist'] = 'session';
		}

		return $instance::doCreate($params);
	}
}

?>