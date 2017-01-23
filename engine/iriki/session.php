<?php

namespace iriki;

class session
{
	public function initiate($params_persist = null)
	{
        $instance = new $params_persist['db_type']();
        $instance::initInstance();

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
