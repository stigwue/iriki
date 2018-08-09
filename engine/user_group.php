<?php

namespace iriki;

class user_group extends \iriki\engine\request
{
	public function create_many($request, $wrap = true)
	{
    	if (!is_null($request))
		{
			$parameters = $request->getData();

			$titles = $parameters['title'];

      		$request->setParameterStatus(array(
				'final' => array('title'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));

			$statuses = array();

			foreach ($titles as $title)
			{
				$request->setData([
					'title' => $title
				]);

				$status = $request->create($request, true);

				if ($status['code'] == 200)
				{
					$state = array(
						'status' => $status['message'],
						'created' => $status['data']
					);

					$statuses[] = $state;
				}
				else
				{
					$state = array(
						'status' => false,
						'created' => null
					);

					$statuses[] = $state;
				}
			}

			return \iriki\engine\response::data($statuses, $wrap);
		}
	}

	public function exists($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('title'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));
			
			$data = $request->read($request, false);

			return \iriki\engine\response::information((count($data) != 0), $wrap);
		}
	}

	public function exists_then($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('title'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array()
			));
			
			$data = $request->read($request, false);

			return \iriki\engine\response::information((count($data) != 0), $wrap, ((count($data) != 0) ? $data[0]['_id'] : null));
		}
	}

	public function read_by_title($request, $wrap = true)
	{
    	if (!is_null($request))
	    {
			return $request->read($request, true);
	    }
	}
}

?>