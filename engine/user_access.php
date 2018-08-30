<?php

namespace iriki;

class user_access extends \iriki\engine\request
{
	public function create($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('user_id', 'user_group_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id', 'user_group_id')
			));
			
			return $request->create($request, $wrap);
		}
	}

	public function user_in_group($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('user_id', 'user_group_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id', 'user_group_id')
			));
			
			$data = $request->read($request, false);

			return \iriki\engine\response::information((count($data) != 0), $wrap);
		}
	}

	public function user_in_group_title($request, $wrap = true)
	{
		//rewrite, now there is a better cross model function
		
    	if (!is_null($request))
		{
			$orig_data = $request->getData();

			//fetch user_id via user/read_by_username
			$user_request = clone $request;
			$user_request->setModelStatus(
	    		array(
					'str' => 'user', //string, model
					'str_full' => '\iriki\user', //string, full model including namespace
					'defined' => true, //boolean, model defined in app or engine config
					'exists' => true, //boolean, model class exists

					'details' => $GLOBALS['APP']['models']['engine']['user'], //array, model description, properties and relationships

					'app_defined' => false, //boolean, model defined in app. otherwise engine
					'action'=> 'read_by_username', //string, action

					'default' => $GLOBALS['APP']['routes']['engine']['default'], //array, default actions

					'action_defined' => true, //boolean, action defined
					'action_default' => false, //boolean, action is default defined
					'action_exists' => true, //boolean, action exists in class

					'action_details' => $GLOBALS['APP']['routes']['engine']['routes']['user'] //array, action description, parameters, exempt
	    		)
	    	);

	    	$user_request->setData([
	            'username' => $orig_data['username']
	        ]);

	        $user_request->setParameterStatus(array(
	          'final' => array('username'),
	          'missing' => array(),
	          'extra' => array(),
	          'ids' => array()
	        ));
	        
	        $user = $user_request->read($user_request, false);

	        if (count($user) != 0)
	        {
				$user = $user[0];
	        }
	        else
	        {
				return \iriki\engine\response::information(false, $wrap);
	        }

			//fetch user_group_id
			$group_request = clone $request;
			$group_request->setModelStatus(
	    		array(
					'str' => 'user_group', //string, model
					'str_full' => '\iriki\user_group', //string, full model including namespace
					'defined' => true, //boolean, model defined in app or engine config
					'exists' => true, //boolean, model class exists

					'details' => $GLOBALS['APP']['models']['engine']['user'], //array, model description, properties and relationships

					'app_defined' => false, //boolean, model defined in app. otherwise engine
					'action'=> 'read_by_title', //string, action

					'default' => $GLOBALS['APP']['routes']['engine']['default'], //array, default actions

					'action_defined' => true, //boolean, action defined
					'action_default' => false, //boolean, action is default defined
					'action_exists' => true, //boolean, action exists in class

					'action_details' => $GLOBALS['APP']['routes']['engine']['routes']['user_group'] //array, action description, parameters, exempt
	    		)
	    	);

	    	$group_request->setData([
	            'title' => $orig_data['title']
	        ]);

	        $group_request->setParameterStatus(array(
	          'final' => array('title'),
	          'missing' => array(),
	          'extra' => array(),
	          'ids' => array()
	        ));
	        
	        $group = $group_request->read($group_request, false);

	        if (count($group) != 0)
	        {
				$group = $group[0];
	        }
	        else
	        {
				return \iriki\engine\response::information(false, $wrap);
	        }

			//check for user_in_group
			//this works now but there may be errors in future
			$request->setData([
				'user_id' => $user['_id'],
				'user_group_id' => $group['_id']
			]);

      		$request->setParameterStatus(array(
				'final' => array('user_id', 'user_group_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id', 'user_group_id')
			));
			
			$data = $request->read($request, false);

			if (count($data) != 0)
			{
				return \iriki\engine\response::information(true, $wrap);
			}
			else {
				return \iriki\engine\response::information(false, $wrap);
			}
		}
	}

	public function user_in_any_group_title($request, $wrap = true)
	{
		//read this user's details to get _id
		//read all groups in a dictionary

		//for each, do a user_in_group, return on first occurence to save time

		$orig_data = $request->getData();
		$groups_to_check = $orig_data['title_array'];
		//this user
		$user = null;

		//read user
		$req = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'user',
                'action' => 'read_by_username',
                'url_parameters' => array(),
                'params' => array(
                    'username' => $orig_data['username']
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

        $state = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $req,
            $request->getTestMode(),
            $request->getSession()
        );

        if ($state['code'] != 200)
        {
        	return \iriki\engine\response::error('Error occured while looking for user. Try again.', $wrap);
        }
        else
        {
        	if (count($state['data']) == 0)
	        {
	        	return \iriki\engine\response::error('User not found.', $wrap);
	        }

	        $user = $state['data'][0];

	        //read all groups. Yeah, all of them.
	        $req = array(
	            'code' => 200,
	            'message' => '',
	            'data' => array(
	                'model' => 'user_group',
	                'action' => 'read_all_dictionary',
	                'url_parameters' => array(),
	                'params' => array()
	            )
	        );

	        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

	        $usr_gr_state = \iriki\engine\route::matchRequestToModel(
	            $GLOBALS['APP'],
	            $model_profile,
	            $req,
	            $request->getTestMode(),
	            $request->getSession()
	        );

	        if ($usr_gr_state['code'] != 200)
	        {
        		return \iriki\engine\response::error('Error occured while looking for user groups. Try again.', $wrap);
	        }
	        else
        	{
	        	if (count($usr_gr_state['data']) == 0)
		        {
		        	return \iriki\engine\response::error('User groups not found.', $wrap);
		        }

		        //loop throug groups, stop once one membership is found
		        foreach ($usr_gr_state['data'] as $user_group_id => $user_group)
		        {
		        	$req = array(
			            'code' => 200,
			            'message' => '',
			            'data' => array(
			                'model' => 'user_access',
			                'action' => 'user_in_group',
			                'url_parameters' => array(),
			                'params' => array(
			                	'user_id' => $user['_id'],
			                	'user_group_id' => $user_group_id
			                )
			            )
			        );

			        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $req);

			        $usr_gr_state = \iriki\engine\route::matchRequestToModel(
			            $GLOBALS['APP'],
			            $model_profile,
			            $req,
			            $request->getTestMode(),
			            $request->getSession()
			        );

			        if ($status['code'] == 200)
			        {
			        	if ($status['message'] == true)
			        	{
			        		//we found membership in this group
			        		return \iriki\engine\response::information(true, $wrap, $user_group_id);
			        	}
			        }
		        }


		        //we searched in all groups, found no membership
        		return \iriki\engine\response::information(false, $wrap);
		    }
        }
	}

	public function remove_user($request, $wrap = true)
	{
    	if (!is_null($request))
		{
      		$request->setParameterStatus(array(
				'final' => array('user_id', 'user_group_id'),
				'missing' => array(),
				'extra' => array(),
				'ids' => array('user_id', 'user_group_id')
			));
			
			$status = $request->delete($request, false);

			if ($status)
			{
				return \iriki\engine\response::information(true, $wrap);
			}
			else {
				return \iriki\engine\response::information(false, $wrap);
			}
		}
	}
}

?>