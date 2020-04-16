<?php

namespace iriki\engine;

/**
* Iriki model, capable of self or inhertited actions
*
*/
class model
{
    /**
    * Get a model's action details.
    * These checks the presence of all possible parameters, using default values if absent.
    * Note that addition of new Iriki parameters have to be recognized here.
    *
    * @param model Chosen model name
    * @param model_status Previously filled model status to edit.
    * @param aroutes Routes to get action details from
    * @return Model status: action details such as parameters, authentication etc
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
                'parameters' => (isset($_route_action[$model_status['action']]['parameters']) ? $_route_action[$model_status['action']]['parameters'] : array()),
                //method, will default to 'ANY'
                //a GET to a POST route will not fail, it just won't read the right parameters
                //or should it?
                'method' => (isset($_route_action[$model_status['action']]['method']) ? $_route_action[$model_status['action']]['method'] : 'ANY'),
                //url parameter decides how parameters in the url are parsed
                //these parameters take precedence over GET and POST ones
                'url_parameters' => (isset($_route_action[$model_status['action']]['url_parameters']) ? $_route_action[$model_status['action']]['url_parameters'] : array()),
                //exempt value of ['*'] will mean all properties are
                'exempt' => (isset($_route_action[$model_status['action']]['exempt']) ? $_route_action[$model_status['action']]['exempt'] : array()),
                //should this route need authentication? default is true
                'authenticate' => (isset($_route_action[$model_status['action']]['authenticate']) ? type::ctype($_route_action[$model_status['action']]['authenticate'], 'boolean') : true),
                //user authentication needed? default is false, if true, a user_id parameter must be included, which must own the session token provided
                'user_authenticate' => (isset($_route_action[$model_status['action']]['user_authenticate']) ? type::ctype($_route_action[$model_status['action']]['user_authenticate'], 'boolean') : false),
                //user group titles user must be part of to be authenticated. part of any will suffice
                'user_group_authenticate' => (isset($_route_action[$model_status['action']]['user_group_authenticate']) ? $_route_action[$model_status['action']]['user_group_authenticate'] : array()),
                //user group titles user must not be part of to be authenticated. part of any will suffice
                'user_group_authenticate_not' => (isset($_route_action[$model_status['action']]['user_group_authenticate_not']) ? $_route_action[$model_status['action']]['user_group_authenticate_not'] : array())
            );

            $model_status['action_defined'] = true;
            $model_status['action_default'] = false;
        }
        //action in default
        else if (isset($model_status['default'][$model_status['action']]))
        {
            $model_status['action_details'] = array(
                'description' => (isset($model_status['default'][$model_status['action']]['description']) ? $model_status['default'][$model_status['action']]['description'] : ''),
                'parameters' => (isset($model_status['default'][$model_status['action']]['parameters']) ? $model_status['default'][$model_status['action']]['parameters'] : array()),
                'method' => (isset($model_status['default'][$model_status['action']]['method']) ? $model_status['default'][$model_status['action']]['method'] : 'ANY'),
                'url_parameters' => (isset($model_status['default'][$model_status['action']]['url_parameters']) ? $model_status['default'][$model_status['action']]['url_parameters'] : array()),
                'exempt' => (isset($model_status['default'][$model_status['action']]['exempt']) ? $model_status['default'][$model_status['action']]['exempt'] : array()),
                'authenticate' => (isset($model_status['default'][$model_status['action']]['authenticate']) ? type::ctype($model_status['default'][$model_status['action']]['authenticate'], 'boolean') : true),
                'user_authenticate' => (isset($model_status['default'][$model_status['action']]['user_authenticate']) ? type::ctype($model_status['default'][$model_status['action']]['user_authenticate'], 'boolean') : false),
                'user_group_authenticate' => (isset($model_status['default'][$model_status['action']]['user_group_authenticate']) ? $model_status['default'][$model_status['action']]['user_group_authenticate'] : array()),
                'user_group_authenticate_not' => (isset($model_status['default'][$model_status['action']]['user_group_authenticate_not']) ? $model_status['default'][$model_status['action']]['user_group_authenticate_not'] : array())
            );

            $model_status['action_defined'] = true;
            $model_status['action_default'] = true;
        }
      }
      return $model_status;
    }


    /**
    * Expand an action's defined parameters. Because sometimes parameters are defined using shortcuts: especially parameters and exempt fields.
    *
    *
    * @param details Model property details
    * @param filter Route filters: parameters and exempt
    * @return Properties: final, missing, extra and ids. Also a flag, explicit_def, defining if parameters were defined explicitly or hinted at.
    * @throw
    */
    public static function doExpandProperty($details, $filter)
    {
      $all_properties = $details['properties'];

      $valid_properties = $filter['parameters'];


      $exempt_properties = null;
      $exempt_properties_count = 0;
      if (isset($filter['exempt']))
      {
        $exempt_properties = $filter['exempt'];
        $exempt_properties_count = count($exempt_properties);
      }

      $explicit_def = true;

      //build valid properties
      if (count($valid_properties) == 0)
      {
          //all properties are valid for parameters = []
          $valid_properties = array_keys($all_properties);

          //this is where we note that specific paramters were
          //not supplied for later
          $explicit_def = false;
      }

      //check exempt properties
      //because of the way this is written, do not exempt
      //parent_id properties, it'll break
      if ($exempt_properties_count == 0)
      {
          //there's no exempt list, carry on
      }
      else
      {
          //note that if exempt holds only *, it means all properties are exempt
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

      return array(
        'valid_properties' => $valid_properties,
        'explicit_def' => $explicit_def
      );
    }


    /**
    * Match a model's defined parameters to those sent via the request
    * Note that a parameter failing a type check is 'missing'
    *
    *
    * @param details Model property details.
    * @param sent Array of methods and their key-value parameters sent via request. Note that this can be edited withing this function. Also note that users might fill sent directly instead of let it be filled by the url parser.
    * @param sent_url Array of url parameters.
    * @param filter Route filters: parameters and exempt
    * @return Properties: final, missing, extra and ids
    * @throw
    */
    public static function doPropertyMatch($details, &$sent, $sent_url, $filter)
    {
      if (isset($sent['ANY']) AND isset($sent[$sent['ANY']]))
      {
        //$sent was filled by the url parser
        $request_method = $filter['method'];

        if (strtoupper($request_method) == 'ANY')
        {
          //replacement for ANY is the server reported request method
          $request_method = $sent['ANY'];
        }

        //add files to selected method's parameters
        if (isset($sent['FILE']))
        {
          foreach ($sent['FILE'] as $key => $value)
          {
            $sent[$request_method][$key] = $value;
          }
        }

        $sent = $sent[$request_method];
      }
      else
      {
        //$sent was filled directly with data
        //proceed
      }

      //parameters work thus:
      //empty valid => all parameters valid except 'exempt'
      //non-empty valid => listed parameters except 'exempt'

      $all_properties = $details['properties'];

      $property_details = Self::doExpandProperty($details, $filter);
      $valid_properties = $property_details['valid_properties'];

      $url_properties = isset($filter['url_parameters']) ? $filter['url_parameters'] : array();

      //build sent properties
      $sent_properties = array_keys($sent);
      $url_sent_properties = $sent_url;

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
      $id_properties = array();
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

              if (!type::is_type($value, $type))
              {
                //a supplied property of different type is deemed missing
                $properties_missing[] = $property;
              }
              else
              {
                //fix type
                $sent[$property] = type::ctype($value, $type);
                $final_properties[] = $property;
              }

              //if key, add to ids
              if ($type == 'key' && !in_array($property, $id_properties))
              {
                $id_properties[] = $property;
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
        'ids' => $id_properties,
        //extra data to pass on to request that parameters were explicitly defined or not, helps with belongsto defined parameters
        'explicit_def' => $property_details['explicit_def']
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
              		//sure you need to pass in ids?
      				'ids' => $initial_request->getParameterStatus()['ids']
      			));

            //do not log this request, makes for cleaner logs
            $new_request->setLog(false);

            //we would have allowed a read whatever the authentication
            //but someone can use brute force to discover some details
            //$new_request->setTestMode(true);

            $found = $new_request->read($new_request, false);

            //revert to original here
            $request = $initial_request;
            if (is_array($found))
            {
              if (count($found) != 0) $existing[] = $property;
            }
          }
        }
      }
      return $existing;
    }

    /**
    * Check a model for 'belongsto' parent relationship.
    * Returns a modified parameter status you may have to update.
    *
    * @param request Request
    * @return Modified parameter status
    * @throw
    */
    public static function doBelongsToRelation($request)
    {
      //todo?: test to see if request can be sent by reference so we can convert parent model ids to mongoid
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
            //the user_session model will need a user_id field for:
            //creates. that's it, anything else need be explicitly defined


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
