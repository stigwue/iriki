<?php

namespace iriki;

class session extends engine\model
{
	public function create($params = null)
	{
		$db_instance = engine\database\mongodb::getInstance();

		$test = engine\database\mongodb::doConnect($db_instance, ['db' => "hr_log"]);

		var_dump($test);
	}
}

?>