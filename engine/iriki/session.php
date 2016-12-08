<?php

namespace iriki;

class session extends engine\model
{
	public function create($db_type, $params = null)
	{
		$instance = new $db_type();

		return $instance::doCreate($params);
	}
}

?>