<?php

namespace iriki;

class session extends engine\model
{
	public function create($db_type, $params = null)
	{
		$instance = new $db_type();
		$instance::initInstance();

		//do validation of params (count check and isset?)
		//if mode is strict and this check fails, do not call create

		$params_persist = array();

		if (!is_null($params))
		{
			$params_persist['data'] = $params;
			$params_persist['persist'] = 'session';
		}

		return $instance::doCreate($params_persist);
	}

	public function read($db_type, $params = null)
	{
		$instance = new $db_type();
		$instance::initInstance();

		$params_persist = array();

		if (!is_null($params))
		{
			$params_persist['data'] = $params;
			$params_persist['persist'] = 'session';
		}

		return $instance::doRead($params_persist);
	}

	public function update($db_type, $params = null)
	{
		$instance = new $db_type();
		$instance::initInstance();

		$params_persist = array();

		if (!is_null($params))
		{
			$params_persist['data'] = $params;
			$params_persist['persist'] = 'session';
		}

		return $instance::doRead($params_persist);
	}
}

?>