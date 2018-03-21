<?php

namespace iriki;

/**
* Iriki log, base class for handling logs.
* Note that a log is a record of the two sides of an action: request and response.
*
*/
class log extends \iriki\engine\request
{

	/*
	Properties include:
		-user _id/session_token
		-object/model
		-action/route
		-timestamp
		-duration (milliseconds)
		-status
		-tag
	An example:
		-fB9aAQKSsW5ehf
		-user
		-create
		-12345678
		-23
		-true
		-username_to_be_created or user_id_created


	Possible stats:
		-time period
		-activity count (total, request, response)
		-status count (success, failure)
		-duration (avg, max, min)
		-object
		-action


	Actions/methods
		-Initiate (so we can start calculating duration)
		-Log (gets and saves duration and the rest of the properties)
		-read (by various criteria)
		-update (Hell Naw! Ok, Nasty C's influence)
		-delete (by criteria defined to save space from time to time)
	*/

	/*
		Initiate does not write to the db.
		It initializes some log properties to be used for later calculations e.g duration)
	*/
	public function initiate($request, $wrap = true)
    {
	    if (!is_null($request))
		{
			$data = $request->getData();

	      	return response::data($data, $wrap);
		}
    }

    public function log($request, $wrap = true)
    {
	    if (!is_null($request))
		{
			$data = $request->getData();

			//calculate duration
			$duration = time(NULL) - $data['timestamp'];

			$data['duration'] = $duration;

			$request->setData($data);

	        $request->setParameterStatus(array(
	            'final' => array(
	            	"user",
					"model",
					"action",
					"timestamp",
					"duration",
					"parent",
					"status",
					"tag"
	            ),
	            'missing' => array(),
	            'extra' => array(),
	            'ids' => array()
	        ));

	      	return $request->create($request, $wrap);
		}
    }
}

?>