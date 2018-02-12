<?php

namespace iriki;

/**
* Iriki model, capable of self or inhertited actions
*
*/
class model
{
    /**
    * Find a model match among supplied models and routes
    * A match is 3 levels:
    * a specific model or alias
    * a specific action for said model
    * a specific set of parameters for said action
    *
    *
    * @param {array} Model status from request made
    * @param {array} Defined models
    * @param {array} Defined routes
    * @returns {array} Model status describing match
    */
    public static function doMatch($model_status, $models = null, $routes = null)
    {
        //see $model_status structure in route->matchUrl

        $model = (isset($model_status['str']) ? $model_status['str'] : null);

        //select model details
        //what if isset($models[$model]) is false?
        //should be caught before this moment
        $_model = $models[$model];

        $model_status['details'] = array(
            'description' => $_model['description'],
            'properties' => $_model['properties'],
            'relationships' => $_model['relationships']
        );

        //now, to find the model's route and other details
        $model_status = Self::getActionDetails($model, $model_status, $routes['routes']);
        //if action_details isn't set, use the default:
        if (!isset($model_status['action_details']))
        {
          //action not found
          $model_status['action_defined'] = false;
          $model_status['action_default'] = false;

          //default to description of model since action does not exist

          $model_status['action_details'] = array(
              'description' => $_model['description']
          );
        }

        return $model_status;
    }

    /**
    * Get a model's action details.
    * These checks the presence of all possible parameters, using default values if absent
    *
    *
    * @param string Chosen model name
    * @param array Previously filled model status
    * @param array Routes to get action details from
    * @returns array Model status: action details such as parameters, authentication etc
    * @throw
    */
    public static function getActionDetails($model, $model_status, $routes)
    {
      if (isset($routes[$model]))
      {
        //model's route found, look up action
        //action can be defined per route or as default for all routes

        $_route_action = $routes[$model];

        //action found in route config
        if (isset($_route_action[$model_status['action']]))
        {
            $model_status['action_details'] = array(
                //action description
                'description' => (isset($_route_action[$model_status['action']]['description']) ? $_route_action[$model_status['action']]['description'] : ''),
                //action 
                'parameters' => $_route_action[$model_status['action']]['parameters'],
                'url_parameters' => (isset($_route_action[$model_status['action']]['url_parameters']) ? $_route_action[$model_status['action']]['url_parameters'] : array()),
                'exempt' => (isset($_route_action[$model_status['action']]['exempt']) ? $_route_action[$model_status['action']]['exempt'] : array()),
                'authenticate' => (isset($_route_action[$model_status['action']]['authenticate']) ? $_route_action[$model_status['action']]['authenticate'] : "true")
            );

            $model_status['action_defined'] = true;
            $model_status['action_default'] = false;
        }
        //action in default
        else if (isset($model_status['default'][$model_status['action']]))
        {
            $model_status['action_details'] = array(
                'description' => (isset($model_status['default'][$model_status['action']]['description']) ? $model_status['default'][$model_status['action']]['description'] : ''),
                'parameters' => $model_status['default'][$model_status['action']]['parameters'],
                'url_parameters' => (isset($_route_action[$model_status['action']]['url_parameters']) ? $_route_action[$model_status['action']]['url_parameters'] : array()),
                'exempt' => (isset($model_status['default'][$model_status['action']]['exempt']) ? $model_status['default'][$model_status['action']]['exempt'] : array()),
                'authenticate' => (isset($_route_action[$model_status['action']]['authenticate']) ? $_route_action[$model_status['action']]['authenticate'] : "true")
            );

            $model_status['action_defined'] = true;
            $model_status['action_default'] = true;
        }
      }
      return $model_status;
    }


    /**
    * Match a model's defined parameters to those sent via the request
    * Note that a parameter failing a type check is 'missing'
    *
    *
    * @param array Model property details
    * @param array Parameters sent via request. Note that they can be edited
    * @param array Parameters sent via url
    * @param array Route filters: parameters and exempt
    * @returns array Properties: final, missing, extra and ids
    * @throw
    */
    public static function doPropertyMatch($details, &$sent, $sent_url, $filter)
    {
        //parameters work thus:
        //empty valid => all parameters valid except 'exempt'
        //non-empty valid => listed parameters except 'exempt'

        $all_properties = $details['properties'];

        $valid_properties = $filter['parameters'];
        $url_properties = $filter['url_parameters'];

        $exempt_properties = (isset($filter['exempt']) ? $filter['exempt'] : null);

        //build sent properties
        $sent_properties = array_keys($sent);
        $url_sent_properties = $sent_url;

        //build valid properties
        if (count($valid_properties) == 0)
        {
            //all properties are valid for parameters = []
            $valid_properties = array_keys($all_properties);
        }

        //check exempt properties
        //the user_session_token might be here
        $exempt_properties_count = count($exempt_properties);
        if ($exempt_properties_count == 0)
        {
            //there's no exempt list, carry on
        }
        else
        {
            if ($exempt_properties_count == 1 && $exempt_properties[0] == '*')
            {
              //unset entire array
              $valid_properties = array();
            }

            for ($i = count($valid_properties) - 1; $i >= 0; $i--)
            {
                //note that if exempt holds only *, it means all properties are exempt
                if (in_array($valid_properties[$i], $exempt_properties))
                {
                    unset($valid_properties[$i]);
                }
            }
        }

        //add url properties to the mix
        //first, check if url_parameters are defined
        if (count($url_properties) != 0)
        {
          //then find the properties in sent and add or replace
          //note that if non valid properties are provided via the url, they will be extra
          $url_index = 0; $sent_url_count = count($sent_url);
          foreach ($url_properties as $url_property)
          {
            //if defined in the url then handle else ignore
            if ($url_index < $sent_url_count)
            {
              //if already sent, will be replaced
              //else added anew
              $sent[$url_property] = $sent_url[$url_index];
            }
            $url_index += 1;
          }
        }

        //check for valid sent properties
        $properties_missing = array();
        $final_properties = array();
        foreach ($valid_properties as $property)
        {
            if (isset($sent[$property]))
            {
                //property is valid and was sent

                //check type? note that the property might be that of a parent model
                if (isset($all_properties[$property]['type']))
                {
                  $type = $all_properties[$property]['type'];  //might be absent

                  $value = $sent[$property];

                  if (type::is_type($value, $type))
                  {
                    //fix type
                    $sent[$property] = type::ctype($value, $type);
                    $final_properties[] = $property;
                  }
                  else
                  {
                    //a supplied property of different type is deemed missing
                    $properties_missing[] = $property;
                  }
                }
                else
                {
                  //ignore type check
                  //assume the user knows what they're doing
                  $final_properties[] = $property;
                }
            }
            else
            {
                $properties_missing[] = $property;
            }
        }

        //check for invalid sent properties
        $extra_properties = array();
        foreach ($sent_properties as $index => $property)
        {
            if (FALSE !== array_search($property, $valid_properties))
            {
                //property sent is valid
            }
            else
            {
                $extra_properties[] = $property;
            }
        }

        $result = array(
          //properties supplied
          'final' => $final_properties,
          //missing properties that should have been supplied
          'missing' => $properties_missing,
          //extra properties that should not have been supplied
          'extra' => $extra_properties,
          //these, especially for mongodb have to be saved as mongoids
          'ids' => array()
        );

        return $result;
    }

    /**
    * Check properties in supplied parameters that need to be unique
    *
    *
    * @param object Request object encapsulating necessary details
    * @returns array Pre-existing properties
    * @throw
    */
    public static function doParameterUniqueCheck($request)
    {
      $existing = array();

      $model_status = $request->getModelStatus();
      $final_properties = $request->getParameterStatus()['final'];
      $final_values = $request->getData();

      $properties = null;
      if ($model_status['action_defined'])
      {
        $properties = $model_status['details']['properties'];
      }
      else if ($model_status['action_default'])
      {
        $properties = $model_status['default']['properties'];
      }

      //$request will be modified, save it here
      $initial_request = clone $request;

      foreach ($final_properties as $index => $property)
      {
        if (isset($properties[$property]))
        {
          $property_details = $properties[$property];

          //check unique
          if (isset($property_details['unique']))
          {
            $new_request = $initial_request;
            $new_request->setData(array($property => $final_values[$property]));
            //parameters
      			$new_request->setParameterStatus(array(
      				'final' => array($property),
      				'missing' => array(),
      				'extra' => array(),
      				'ids' => $initial_request->getParameterStatus()['ids']
      			));

            $found = $new_request->read($new_request, false);

            //revert to original here
            $request = $initial_request;
            if (count($found) != 0) $existing[] = $property;
          }
        }
      }
      return $existing;
    }

    /**
    * Check a model for 'belongsto' parent relationship.
    * Returns a modified parameter statuses you may have to update.
    *
    * @param object Request
    * @returns array Modified parameter status
    * @throw
    */
    public static function doBelongsToRelation($request)
    {
      //test to see if request can be sent by reference so we can convert parent model ids to mongoid
      $parameters = $request->getParameterStatus();

      $belongsto = array();
      $belongsto = (isset($request->getModelStatus()['details']['relationships']['belongsto']) ? $request->getModelStatus()['details']['relationships']['belongsto'] : array());

      $request_data = $request->getData();

      if (count($belongsto) != 0)
      {
        foreach ($belongsto as $parent_model)
        {
          //all parent models must have a 'parent_model + id_field' parameter supplied
          //we could go as far as to check that the parent model exists but... maybe not


            //the plan is simple
            //if the model 'user_session' belongsto 'user'
            //the user_session model will have a user_id field, get it
            //then find the user details with the id supplied

          $db_instance = $request::getDBInstance();

          $property_identifier = $parent_model . $db_instance::ID_FIELD;
          
          if (isset($request_data[$property_identifier]))
          {
            //add to final parameters
            $parameters['final'][] = $property_identifier;

            //add to ids data
            $parameters['ids'][] = $property_identifier;

            //pull out supplied from extra parameters
            $extra_key = array_search($property_identifier, $parameters['extra']);
            if ($extra_key !== FALSE)
            {
              unset($parameters['extra'][$extra_key]);
            }
          }
          else
          {
            //note that it is missing
            $parameters['missing'][] = $property_identifier;
          }
        }
      }
      
      return $parameters;
    }

    /**
    * Check a model for 'hasmany' child relationship.
    * Will have to make several reads up to a recursivity limit.
    *
    * @param object Request
    * @returns array Parameter status for now, should change soon
    * @throw
    */
    public static function doHasManyRelation($request, $recursivity = 1)
    {
      //test to see if request can be sent by reference so we can convert parent model ids to mongoid
      $parameters = $request->getParameterStatus();

      $hasmany = array();
      $hasmany_data = array();
      $hasmany = (isset($request->getModelStatus()['details']['relationships']['hasmany']) ? $request->getModelStatus()['details']['relationships']['hasmany'] : array());

        //the plan is simple
        //if the model 'user' hasmany 'user_session'
        //the user model will have an _id field: the user_id, get it
        //then find the user_sessions with the user_id supplied

      /*if (count($hasmany) != 0)
      {
        $parent_model = $request->getModelStatus()['str']; //this present model
        $db_instance = &$request::getDBInstance();
        $property_identifier = $parent_model . $db_instance::ID_FIELD;
        $property_value = $request->getData()[$db_instance::ID_FIELD];
        

        foreach ($hasmany as $child_model)
        {
          //build request to child model

          //read data from child model
          //$hasmany_data[$child_model] = $request->

          //modify parameters
          //final remains same
          //missing remains same so already reported if not count 0
          //extra remains same, but report it as it hasn't been yet
        }
      }*/

      return $parameters;
    }

}

?>
