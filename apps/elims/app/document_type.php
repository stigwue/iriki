<?php

namespace elims;

class document_type extends \iriki\model
{
	public function read_all($params_persist = null)
    {
        $instance = new $params_persist['db_type']();
        $instance::initInstance();

        if (!is_null($params_persist))
        {
            return $instance::doRead($params_persist);
        }

    }
}

?>