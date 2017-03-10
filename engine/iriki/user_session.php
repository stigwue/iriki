<?php

namespace iriki;

class user_session
{
	private static function generatetoken()
	{
		$seed = time(NULL); //uniqid(time(NULL), true);

		return (
			\PseudoCrypt::hash($seed, 10) .
			\PseudoCrypt::hash($seed, 4)
		);
	}

	public function initiate($params_persist = null)
	{
		$instance = new $params_persist['db_type']();
		$instance::initInstance();

		//generate token
		$params_persist['data']['token'] = Self::generatetoken();

		//get ip address
		$params_persist['data']['ip'] = $_SERVER['SERVER_ADDR'];

		//do validation of params (count check and isset?)
		//if mode is strict and this check fails, do not call create

		//add created and modified timestamps?

		if (!is_null($params_persist))
		{
			return $instance::doCreate($params_persist);
		}
	}
}

?>
