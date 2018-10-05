<?php

namespace iriki;

class statistics extends \iriki\engine\request
{
    const SEPERATOR = ',';

    private static function array_subtract($first, $second)
    {
        $f = count($first);
    	if ($f != count($second))
    	{
    		//zeros
            return array_fill(1, $f, 0);
    	}
    	else
    	{
            $result = array();
            for ($i=0; $i<$f; $i++)
            {
                $result[] = $first[$i] - $second[$i];
            }

            return $result;
    	}
    }

    private static function delta($stats)
    {
        //reduce to changes
        //this assumes that the changes are single valued or comma seperated
        $deltas = array();
        $count = count($stats);

        for ($i = 0; $i < $count; $i++)
        {
            if ($i == 0)
            {
                //first, use current as cummulative so we get zero
                $delta = implode(Self::SEPERATOR,
                    Self::array_subtract(
                        explode(Self::SEPERATOR, $stats[$i]['value']),
                        explode(Self::SEPERATOR, $stats[$i]['value'])
                    )
                );
            }
            else
            {
                //deduct current from past
                $delta = implode(Self::SEPERATOR,
                    Self::array_subtract(
                        explode(Self::SEPERATOR, $stats[$i]['value']),
                        explode(Self::SEPERATOR, $stats[$i-1]['value'])
                    )
                );
            }

            $deltas[] = array(
                '_id' => $stats[$i]['_id'],
                'code' => $stats[$i]['code'],
                'timestamp' => $stats[$i]['timestamp'],
                'label' => $stats[$i]['label'],
                'value' => $delta
            );
        }

        return $deltas;
    }

    public function create($request, $wrap = true)
    {
        $request->setParameterStatus([
            'final' => array('code', 'timestamp', 'label', 'value'),
            'extra' => array(),
            'missing' => array(),
            'ids' => array()
        ]);

        return $request->create($request, $wrap);
    }

    public function read_by_code($request, $wrap = true)
    {
        $request->setParameterStatus([
            'final' => array('code'),
            'extra' => array(),
            'missing' => array(),
            'ids' => array()
        ]);

        $request->setMeta(['sort' => array('created' => +1)]);

        return $request->read($request, $wrap);
    }

    public function read_by_code_range($request, $wrap = true)
    {
        $data = $request->getData();

        $new_data = array(
            'code' => $data['code'],
            'created' => array(
                '$gte' => $data['from_timestamp'],
                '$lte' => $data['to_timestamp']
            )
        );

        $request->setData($new_data);

        $request->setParameterStatus([
            'final' => array('code', 'created'),
            'extra' => array(),
            'missing' => array(),
            'ids' => array()
        ]);

        $request->setMeta(['sort' => array('created' => +1)]);

        return $request->read($request, $wrap);
    }

    public function read_by_code_delta($request, $wrap = true)
    {
        $request->setParameterStatus([
            'final' => array('code'),
            'extra' => array(),
            'missing' => array(),
            'ids' => array()
        ]);

        $request->setMeta(['sort' => array('created' => +1)]);

        $stats = $request->read($request, false);

        $deltas = Self::delta($stats);

        return \iriki\engine\response::data($deltas, $wrap);

    }

    public function read_by_code_delta_range($request, $wrap = true)
    {
        $data = $request->getData();

        $new_data = array(
            'code' => $data['code'],
            'created' => array(
                '$gte' => $data['from_timestamp'],
                '$lte' => $data['to_timestamp']
            )
        );

        $request->setData($new_data);

        $request->setParameterStatus([
            'final' => array('code', 'created'),
            'extra' => array(),
            'missing' => array(),
            'ids' => array()
        ]);

        $request->setMeta(['sort' => array('created' => +1)]);

        $stats = $request->read($request, false);

        $deltas = Self::delta($stats);

        return \iriki\engine\response::data($deltas, $wrap);

    }
}

?>
