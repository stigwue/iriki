<?php

namespace iriki;

/**
* Iriki log, base class for handling logs.
*
*/
class log extends \iriki\engine\request
{

	/*
	Possible stats:
		-time period
		-activity count (total, request, response)
		-status count (success, failure)
		-duration (avg, max, min)
		-object
		-action
	*/

	public function request($request, $wrap = true)
    {
	    if (!is_null($request))
		{
			$status = $request->create($request, $wrap);
			return $status;
		}
		else
		{
			return \iriki\engine\response::error('Request not initialised.');
		}
    }

    public function response($request, $wrap = true)
    {
	    if (!is_null($request))
		{
			$status = $request->create($request, $wrap);
			return $status;
		}
		else
		{
			return \iriki\engine\response::error('Request not initialised.');
		}
    }

    public function read_timestamp($request, $wrap = true)
    {
        $data = $request->getData();

        //filter
        $query_data = array(
            'created' => array(
                '$gte' => (int) $data['from_timestamp']
            )
        );
        $request->setData($query_data);

        $request->setParameterStatus(array(
          'final' => array('created'),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta(['sort' => array('created' => -1)]);

        return $request->read($request, $wrap);
    }

    public function read_index($request, $wrap = true)
    {
        $data = $request->getData();

        $request->setData([]);

        $request->setParameterStatus(array(
          'final' => array(),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta([
        	'limit' => $data['index'],
        	'sort' => array('created' => -1)
        ]);

        return $request->read($request, $wrap);
    }
}

?>