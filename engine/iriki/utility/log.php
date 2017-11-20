<?php

namespace iriki;

/**
* Iriki log, base class for handling logs.
*
*/

class log
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
		-Log (gets duration and the rest of the properties)
		-save (create)
		-read (by various criteria)
		-update (Hell Naw! Ok, Nasty C's influence)
		-delete (by criteria defined to save space from time to time)
	*/

}

?>