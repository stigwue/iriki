<?php

namespace cashcrow;

class merchant extends \iriki\engine\model
{
	public function info($status = null, $json = false)
	{
        if (is_null($status))
        {
            $status = array('data' => array());
        }

        if ($json)
        {
            return json_encode($status);
        }
        else
        {
            return $status;
        }
	}

    public function create($args)
    {
    	
    }
}

?>