<?php

namespace iriki;

class session extends engine\model
{
	public function create($db, $params = null)
	{
		$instance = $db->getInstance();

		return $instance->doCreate($db, $params);
	}
}

?>