<?php

namespace iriki;

/**
* Iriki log, base class for handling logs.
*
*/
class log extends \iriki\request
{

	/*
	Properties include:
		-user _id/session_token
		-object/model
		-action/route
		-timestamp
		-duration (milliseconds)
		-request
		-response
		-status
		-tag
	An example:
		-fB9aAQKSsW5ehf
		-user
		-create
		-12345678
		-23
		-username
		-created user_id 
		-true
		-xyz


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
			$duration = time(NULL) - $data['duration'];
			$data['duration'] = $duration;

	      	return $request->create($request, $wrap);
		}
    }
}

?>