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

    private static function statify($logs, $code, $wrap, $model = '*', $action = '*', $tag = '', $period = 86400)
    {
        //parse logs into stat format
        $count = count($logs);

        if ($count != 0)
        {
            $next_stamp = $logs[$count - 1]['created'] + $period;

            $response = array();

            $stat_array = array(
                'all' => 0,
                'request' => 0,
                'ok' => 0,
                'error' => 0,
                'auth' => 0,
                'other' => 0
            );

            $left_over = false;

            //var_dump($next_stamp, $count);

            for ($i = $count-1; $i >= 0; --$i)
            {
                $log = $logs[$i];

                //check to reset timestamp
                if ($log['created'] <= $next_stamp)
                {
                    //group them into the period
                    //requests, 200, 400, 401 and other
                    //parse them for counting
                    $stat_array['all'] += 1;

                    if ($log['parent'] == '')
                    {
                        $stat_array['request'] += 1;
                    }
                    else if (\iriki\engine\type::ctype($log['tag'], 'number') == \iriki\engine\response::OK)
                    {
                        $stat_array['ok'] += 1;
                    }
                    else if (\iriki\engine\type::ctype($log['tag'], 'number') == \iriki\engine\response::ERROR)
                    {
                        $stat_array['error'] += 1;
                    }
                    else if (\iriki\engine\type::ctype($log['tag'], 'number') == \iriki\engine\response::AUTH)
                    {
                        $stat_array['auth'] += 1;
                    }
                    else
                    {
                        $stat_array['other'] += 1;
                    }

                    $left_over = true;
                }
                else
                {

                    $single_response = array(
                        'code' => $code,
                        'timestamp' => date('g:ia, jS M Y', $next_stamp),
                        'label' => "Requests|OK Response|Error Response|Authentication error Response|Other Response",
                        'value' => $stat_array['request'] . ',' . $stat_array['ok'] . ',' . $stat_array['error'] . ',' . $stat_array['auth'] . ',' . $stat_array['other']
                    );

                    $response[] = $single_response;

                    $next_stamp = $next_stamp + $period;

                    //var_dump($next_stamp);

                    //add current sum to $response, start another
                    $stat_array = array(
                        'all' => 0,
                        'request' => 0,
                        'ok' => 0,
                        'error' => 0,
                        'auth' => 0,
                        'other' => 0
                    );

                    $left_over = false;
                }
            }

            if ($left_over)
            {
                $single_response = array(
                    'code' => $code,
                    'timestamp' => date('g:ia, jS M Y', $next_stamp),
                    'label' => "Requests|OK Response|Error Response|Authentication error Response|Other Response",
                    'value' => $stat_array['request'] . ',' . $stat_array['ok'] . ',' . $stat_array['error'] . ',' . $stat_array['auth'] . ',' . $stat_array['other']
                );

                $response[] = $single_response;
            }

            return \iriki\engine\response::data($response, $wrap);
        }
        else
        {
            return \iriki\engine\response::data(array(), $wrap);
        }
    }

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

    public function read_filter($request, $wrap = true)
    {
        $data = $request->getData();

        $params = array(
            'created' => array(
                '$gte' => (int) $data['from_timestamp']
            )
        );

        if ($data['model'] !== '*')
        {
            $params['model'] = $data['model'];
        }
        if ($data['action'] !== '*')
        {
            $params['action'] = $data['action'];
        }
        if ($data['tag'] !== '*')
        {
            $params['tag'] = (int) $data['tag'];
        }

        //filter
        $request->setData($params);

        $request->setParameterStatus(array(
          'final' => array_keys($params),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta([
            'sort' => array('created' => -1)
        ]);

        $logs = $request->read($request, false);

        if (\iriki\engine\type::ctype($data['statify'], 'boolean') == true)
        {
            //parse logs into stat format
            return Self::statify($logs, $data['code'], $wrap, $data['model'], $data['action'], (int) $data['tag'], \iriki\engine\type::ctype($data['period'], 'number'));
        }
        else
        {
            return \iriki\engine\response::data($logs, $wrap);
        }
    }

    public function read_filter_count($request, $wrap = true)
    {
        $data = $request->getData();

        $params = array();

        if ($data['model'] !== '*')
        {
            $params['model'] = $data['model'];
        }
        if ($data['action'] !== '*')
        {
            $params['action'] = $data['action'];
        }
        if ($data['tag'] !== '*')
        {
            $params['tag'] = (int) $data['tag'];
        }

        //filter
        $request->setData($params);

        $request->setParameterStatus(array(
          'final' => array_keys($params),
          'missing' => array(),
          'extra' => array(),
          'ids' => array()
        ));

        $request->setMeta([
            'limit' => $data['count'],
            'sort' => array('created' => -1)
        ]);

        $logs = $request->read($request, false);

        if (\iriki\engine\type::ctype($data['statify'], 'boolean') == true)
        {
            //parse logs into stat format
            return Self::statify($logs, $data['code'], $wrap, $data['model'], $data['action'], (int) $data['tag'], \iriki\engine\type::ctype($data['period'], 'number'));
        }
        else
        {
            return \iriki\engine\response::data($logs, $wrap);
        }
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
        $model_count = 0;

        //approximate with happrox
        $obj = new \Happrox();
        \Happrox::setDurationBase($obj, time(NULL));

        if (count($latest_log) != 0)
        {
            $stamp = time(NULL) - $latest_log[0]['created'];
        }

        //read model count
        $req = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'app',
                'action' => 'models',
                'url_parameters' => array(),
                'params' => array()
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $req,
            $request->getTestMode(), //test mode
            $request->getSession()
        );

        if ($status['code'] == 200)
        {
            $model_count = count($status['data']);
        }

        return \iriki\engine\response::data(
            [
                'entries' => \Happrox::number($obj, $count),
                'latest' => \Happrox::duration($obj, $stamp),
                'models' => \Happrox::number($obj, $model_count)
            ],
            $wrap
        );
    }
}

?>