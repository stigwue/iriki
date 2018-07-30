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

    public function read_count($request, $wrap = true)
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
        	'limit' => $data['count'],
        	'sort' => array('created' => -1)
        ]);

        return $request->read($request, $wrap);
    }

    public function summary($request, $wrap = true)
    {
        $request->setData([]);

        //get count
        $request->setParameterStatus(array(
          'final' => array(),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta([
            'count' => true
        ]);

        $count = $request->read($request, false);

        //get latest
        $request->setParameterStatus(array(
          'final' => array(),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta([
            'sort' => array('created' => -1),
            'limit' => 1
        ]);

        $latest_log = $request->read($request, false);
        $stamp = 0;

        //approximate with happrox
        $obj = new \Happrox();
        \Happrox::setDurationBase($obj, time(NULL));

        if (count($latest_log) != 0)
        {
            $stamp = time(NULL) - $latest_log[0]['created'];
        }

        return \iriki\engine\response::data(
            [
                'entries' => \Happrox::number($obj, $count),
                'latest' => \Happrox::duration($obj, $stamp)
            ],
            $wrap
        );
    }
}

?>