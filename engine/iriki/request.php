<?php

namespace iriki\engine;

//response
require_once(__DIR__ . '/response.php');

/**
* Iriki request, user written classes inherit this.
* see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
*
*/
class request
{
    /**
    * Database handler instance.
    *
    */
    private static $_db_instance = null;

    /**
    * Test mode, boolean value to determine if tests are been run and as such, ignore authentication.
    *
    */
    private $_test_mode;

    /**
    * Session token, passed around in header.
    *
    */
    private $_session_token;

    /**
    * Authentication state: user, group and group-not.
    *
    */
    private $_authentication;

    /**
    * Model and action status, see definition in route::matchUrl
    *
    */
    private $_model_status;

    /**
    * Parameters expected by request in their states.
    *
    */
    private $_parameter_status;

    /**
    * Data to be handled by request. An associative array.
    *
    */
    private $_data;

    /**
    * Metadata to be used by request.
    * For instance, an associative array for sorting.
    *
    */
    private $_meta;

    /**
    * Tag, other data to be used.
    *
    */
    private $_tag;

    /**
    * Perform logging? Boolean value.
    *
    */
    private $_log = true;

    /**
    * Gets the test mode value
    *
    * @return Test mode
    * @throw
    */
    public function getTestMode()
    {
      return $this->_test_mode;
    }

    /**
    * Sets the test mode
    *
    * @param test_mode Test mode boolean value
    * @return Test mode
    * @throw
    */
    public function setTestMode($test_mode)
    {
      $this->_test_mode = $test_mode;
      return $this->_test_mode;
    }

    /**
    * Gets the session token
    *
    * @return Session token
    * @throw
    */
    public function getSession()
    {
      return $this->_session_token;
    }

    /**
    * Sets the Session token
    *
    * @param session_token Session token
    * @return Session token
    * @throw
    */
    public function setSession($session_token)
    {
      $this->_session_token = $session_token;
      return $this->_session_token;
    }

    /**
    * Gets the authentication state
    *
    * @return Authentication state
    * @throw
    */
    public function getAuthentication()
    {
      return $this->_authentication;
    }

    /**
    * Sets the authentication state
    *
    * @param authentication Authentication state: array of user (boolean), group (array) and group_not (array) keys.
    * @return Authentication state
    * @throw
    */
    public function setAuthentication($authentication)
    {
      $this->_authentication = $authentication;
      return $this->_authentication;
    }

    /**
    * Gets the database instance.
    *
    *
    * @return Database instance object.
    */
    public static function getDBInstance()
    {
      return Self::$_db_instance;
    }

    /**
    * Sets the database instance.
    *
    * @param objDB Database object.
    * @return Database instance object.
    */
    public static function setDBInstance($objDB)
    {
      Self::$_db_instance = $objDB;
      return Self::$_db_instance;
    }

    /**
    * Gets the model status
    *
    * @return Model status.
    * @throw
    */
    public function getModelStatus()
    {
      return $this->_model_status;
    }

    /**
    * Sets the model status.
    *
    * @param model_status Model status.
    * @return Model status.
    * @throw
    */
    public function setModelStatus($model_status)
    {
      $this->_model_status = $model_status;
      return $this->_model_status;
    }

    /**
    * Gets the parameter status/states.
    *
    * @return Parameter status.
    * @throw
    */
    public function getParameterStatus()
    {
      return $this->_parameter_status;
    }

    /**
    * Sets the parameter status/states.
    *
    * @param parameter_status Parameter status.
    * @return Parameter status.
    * @throw
    */
    public function setParameterStatus($parameter_status)
    {
      $this->_parameter_status = $parameter_status;
      return $this->_parameter_status;
    }

    /**
    * Gets the request model.
    *
    * @return Request model.
    * @throw
    */
    public function getModel()
    {
      return $this->_model_status['str'];
    }

    /**
    * Sets the request model.
    *
    * @param model Request model.
    * @return Request model.
    * @throw
    */
    public function setModel($model)
    {
      $this->_model_status['str'] = $model;
      return $this->_model_status['str'];
    }

    /**
    * Gets the request data.
    *
    * @return Request data.
    * @throw
    */
    public function getData()
    {
      return $this->_data;
    }

    /**
    * Sets the request data.
    *
    * @param data Request data.
    * @return Request data.
    * @throw
    */
    public function setData($data)
    {
      $this->_data = $data;
      return $this->_data;
    }

    /**
    * Add to the request data dictionary. This might replace an existing parameter.
    *
    * @param parameter Request parameter.
    * @return Request data.
    * @throw
    */
    public function addToData($parameter, $value)
    {
      $this->_data[$parameter] = $value;
      return $this->_data;
    }

    /**
    * Remove from the request data dictionary.
    *
    * @param parameter Request parameter.
    * @return Request data.
    * @throw
    */
    public function removeFromData($parameter)
    {
      unset($this->_data[$parameter]);
      return $this->_data;
    }

    /**
    * Gets the request metadata.
    *
    * @return Request metadata.
    * @throw
    */
    public function getMeta()
    {
      return $this->_meta;
    }

    /**
    * Sets the request metadata.
    *
    * @param meta Request metadata.
    * @return Request metadata.
    * @throw
    */
    public function setMeta($meta)
    {
      $this->_meta = $meta;
      return $this->_meta;
    }

    /**
    * Gets the request tag.
    *
    * @return Request tag.
    * @throw
    */
    public function getTag()
    {
      return $this->_tag;
    }

    /**
    * Sets the request tag.
    *
    * @param meta Request tag.
    * @return Request tag.
    * @throw
    */
    public function setTag($tag)
    {
      $this->_tag = $tag;
      return $this->_tag;
    }

    /**
    * Gets the request log flag.
    *
    * @return Request log flag.
    * @throw
    */
    public function getLog()
    {
      return $this->_log;
    }

    /**
    * Sets the request log flag.
    *
    * @param log Request log flag.
    * @return Request log flag.
    * @throw
    */
    public function setLog($log)
    {
      $this->_log = $log;
      return $this->_log;
    }

    /**
     * Intercepts authentication or some other last minute errors reported.
     * Note that code is only set if an error occurs (code is then an error code) or wrapping is specified.
     *
     * @param result Result returned from database operation
     * @param default_response Response type to use if all goes well (data, information or error)
     * @param wrap Flag to perform a response wrap or not.
     * @param log_object Request log object for continuity in loggin response.
     * @param do_log Perform request logging or not. Might make it false by default?
     * @return Array of an error encountered flag and final result.
     */
    public static function catchError($result, $default_response, $wrap, $log_object, $do_log)
    {
      $new_result = array(
        'error' => false,
        'result' => $result
      );

      //if code and message are set,
      //most probably we are looking at an error
      if (isset($result['code']) && isset($result['message']))
      {
        //something error'd
        switch ($result['code'])
        {
          //authentication failed
          case response::AUTH:
            if ($do_log) Self::log(null, time(), $log_object, response::AUTH);

            $new_result['error'] = true;
            $new_result['result'] = \iriki\engine\response::buildFor('auth', 'User session token invalid or expired.', $wrap);
          break;

          //expecting a particular parameter which is missing
          //especially ids of parent models
          case response::ERROR:
            if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

            if ($result['message'] == 'missing_parameter')
            {
              $new_result['error'] = true;
              $new_result['result'] = \iriki\engine\response::buildFor('error', 'Parameter missing or of wrong type.', $wrap);
            }
            else
            {
              //some other error
              $new_result['error'] = true;
              $new_result['result'] = \iriki\engine\response::buildFor('error', 'Some other error occurred.', $wrap);
            }
          break;

          default:
            //nothing was caught?
            if ($do_log) Self::log(null, time(), $log_object, \iriki\engine\response::responseToCode($default_response));

            $new_result['error'] = false;
            $new_result['result'] = \iriki\engine\response::buildFor($default_response, $result, $wrap);
          break;
        }
      }
      //if they are not set, we are returning null or some stuff, just return as is (if we are not to wrap)
      //else, we should wrap with the appropriate response markers
      else
      {
        if ($do_log) Self::log(null, time(), $log_object, \iriki\engine\response::responseToCode($default_response));

        //no errors
        $new_result['error'] = false;
        $new_result['result'] = \iriki\engine\response::buildFor($default_response, $result, $wrap);
      }

      return $new_result;
    }

    /**
    * Perform a create action on a request.
    * Before a request action is called, request data is filled
    * and the keys (parameters) are in one of three groups: final, missing, extra
    * before relationship checks are done, parent model properties are in extra
    * a relationship check should move valid ones from extra into final or insert into missing
    * a model action is not performed until missing and extra is zero
    * --"fax!" quoth the penguin
    *
    * For each of the CRUD actions, do (if available) pre and post actions/checks
    * relationship | C | R | U | D
    * unique       | v | x | x | x
    * belongsto    | v | x | v | x
    * hasmany      | x | v | x | v
    *
    * TODO: possible hack for overriding uniques in updates
    *
    * Note that there's a recursivity variable to limit relationship checks
    *
    * @param request Request object
    * @param wrap Wrap results with descriptors
    * @return Response object or data
    * @throw
    */
    public function create($request, $wrap = true)
    {
      $db = Self::$_db_instance;
      $do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      //unique
      $matching = model::doParameterUniqueCheck($request);
      if (count($matching) != 0)
      {
          $result = array();

          if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

          if (!$wrap) return $result;
          else return response::error(
            response::showMissing($matching, 'parameter', 'of wrong type (or type check could not be performed)'),
            $wrap);
      }

      //belongsto
      $parameter_status = model::doBelongsToRelation($request);

      //replace with modified
      $request->setParameterStatus($parameter_status);

      $missing_parameters = count($parameter_status['missing']);
      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0 OR $missing_parameters != 0)
      {
        if ($missing_parameters != 0)
        {
            if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

            return response::error(
              response::showMissing($parameter_status['missing'], 'relationship parameter', 'missing'),
              $wrap);
        }
        if ($extra_parameters != 0)
        {
          if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

          return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'), $wrap);
        }
      }

      //hasmany

      $result = $db::doCreate($request);

      //intercept errors so as to display them accordingly
      $final_result = Self::catchError($result, 'information', $wrap, $log_object, $do_log);

      return $final_result['result'];
    }

    /**
    * Perform a read action on a request
    *
    * @param request Request object
    * @param wrap Wrap results with descriptors
    * @return Response object or data
    * @throw
    */
    public function read($request, $wrap = true)
    {
      $db = Self::$_db_instance;
      $do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      //unique
      //belongsto
      //hasmany
      $parameter_status = model::doHasManyRelation($request);
      //read should pick up any "hasmany" models up to x recursivity

      //replace with modified
      $request->setParameterStatus($parameter_status);

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        if ($do_log) Self::log(null, time(), $log_object, response::ERROR);
        
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $meta = $request->getMeta();
      
      $result = $db::doRead($request, $meta);

      //intercept errors so as to display them accordingly
      $final_result = Self::catchError($result, 'data', $wrap, $log_object, $do_log);

      return $final_result['result'];
    }

    /**
    * Perform a 'read all' action on a request
    *
    * @param request Request object
    * @param wrap Wrap results with descriptors
    * @return Response object or data
    * @throw
    */
    public function read_all($request, $wrap = true)
    {
      $db = Self::$_db_instance;$do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      $parameter_status = $request->getParameterStatus();

      //replace with modified
      $request->setParameterStatus($parameter_status);

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        if ($do_log) Self::log(null, time(), $log_object, response::ERROR);
        
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $request->setData(array());

      $meta = $request->getMeta();

      //read should also pick up any "hasmany" models up to x recursivity

      $result = $db::doRead($request, $meta);

      $final_result = Self::catchError($result, 'data', $wrap, $log_object, $do_log);

      return $final_result['result'];
    }

    /**
    * Perform a 'read all' action on a request, returning a dictionary of _id -> objects.
    *
    * @param request Request object
    * @param wrap Wrap results with descriptors
    * @return Response object or data dictionary
    * @throw
    */
    public function read_all_dictionary($request, $wrap = true)
    {
      $db = Self::$_db_instance;
      $do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      $parameter_status = $request->getParameterStatus();

      //replace with modified
      $request->setParameterStatus($parameter_status);

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        if ($do_log) Self::log(null, time(), $log_object, response::ERROR);
        
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $request->setData(array());

      $meta = $request->getMeta();

      //read should also pick up any "hasmany" models up to x recursivity

      $result = $db::doRead($request, $meta);

      //tricky bit here, we need to know if any errors where intercepted
      //before we attempt dictionary formatting
      //else empty dictionaries will be returned instead of the error message
      $final_result = Self::catchError($result, 'data', $wrap, $log_object, $do_log);

      if ($final_result['error'] == true)
      {
        return $final_result['result'];
      }
      else
      {
        //convert to dictionary
        $dictionary = array();
        foreach ($result as $single_result)
        {
          $key_property = '_id';

          if (isset($single_result[$key_property]))
          {
            $dictionary[$single_result[$key_property]] = $single_result;
          }
          else
          {
            //ignore
          }
        }

        return \iriki\engine\response::data($dictionary, $wrap);
      }
    }

    /**
    * Perform a 'count' action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @returns object Response object or data
    * @throw
    */
    public function count($request, $wrap = true)
    {
      $db = Self::$_db_instance;
      $do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      $parameter_status = $request->getParameterStatus();

      //replace with modified
      $request->setParameterStatus($parameter_status);

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        if ($do_log) Self::log(null, time(), $log_object, response::ERROR);
        
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $request->setData(array());

      $meta = $request->setMeta([
        'count' => true
      ]);

      $result = $db::doRead($request, $meta);

      $final_result = Self::catchError($result, 'data', $wrap, $log_object, $do_log);

      return $final_result['result'];
    }

    /**
    * Perform an update action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @returns object Response object or data
    * @throw
    */
    public function update($request, $wrap = true)
    {
      $db = Self::$_db_instance;
      $do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      //unique
      //belongsto: only if request has no explicitly defined parameters (stored in tag)
      if ($request->getTag() == true)
      {
        //explicitly defined
      }
      else
      {
        $parameter_status = model::doBelongsToRelation($request);

        //replace with modified
        $request->setParameterStatus($parameter_status);

        $missing_parameters = count($parameter_status['missing']);
        $extra_parameters = count($parameter_status['extra']);

        if ($extra_parameters != 0 OR $missing_parameters != 0)
        {
          if ($missing_parameters != 0)
          {
            if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

            return response::error(
              response::showMissing($parameter_status['missing'], 'relationship parameter', 'missing'),
              $wrap);
          }
          if ($extra_parameters != 0)
          {
            if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

            return response::error(
            response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
            $wrap);
          }
        }
      }
      
      //hasmany

      $result = $db::doUpdate($request);

      $final_result = Self::catchError($result, 'information', $wrap, $log_object, $do_log);

      return $final_result['result'];
    }

    /**
    * Perform a delete action on a request.
    *
    * @param request Request object.
    * @param wrap Wrap results with descriptors.
    * @return Response object or data
    * @throw
    */
    public function delete($request, $wrap = true)
    {
      $db = Self::$_db_instance;
      $do_log = $request->getLog();
      $log_object = null;
      if ($do_log) $log_object = Self::log($request, time());

      //unique
      //belongsto
      //hasmany
      $parameter_status = model::doHasManyRelation($request);
      //delete should affect any "hasmany" models, ignoring recursivity limits

      //replace with modified
      $request->setParameterStatus($parameter_status);

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        if ($do_log) Self::log(null, time(), $log_object, response::ERROR);

        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $result = $db::doDelete($request);
      
      $final_result = Self::catchError($result, 'information', $wrap, $log_object, $do_log);

      return $final_result['result'];
    }

    /**
    * Log an action.
    *
    * @param request Request details. Will be null for responses.
    * @param timestamp Action timestamp.
    * @param parent_obj Parent log object. Default null.
    * @param tag Log tag. Default empty.
    * @return Log object.
    * @throw
    */
    private static function log($request, $timestamp, $parent_obj=null, $tag='')
    {
      $request_details = array();

      //requests
      if (!is_null($request) AND is_null($parent_obj))
      {
        //do not log a log action or we will be locked in an infinite loop
        $model = $request->getModelStatus()['str'];
        if ($model == 'log') return;

        //null parent object, build request_details from request supplied
        $request_details = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'log',
                'action' => 'request',
                'url_parameters' => array(),
                'params' => array(
                    'model' => $request->getModelStatus()['str'],
                    'action' => $request->getModelStatus()['action'],
                    'timestamp' => $timestamp,
                    'parent' => '',
                    'tag' => $tag
                )
            )
        );
      }
      //response
      else if (is_null($request) AND !is_null($parent_obj))
      {
        //do not log a log action or we will be locked in an infinite loop
        $model = $parent_obj['model'];
        if ($model == 'log') return;

        //build request details from parent_obj
        $request_details = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'log',
                'action' => 'request',
                'url_parameters' => array(),
                'params' => array(
                    'model' => $parent_obj['model'],
                    'action' => $parent_obj['action'],
                    'timestamp' => $timestamp,
                    'parent' => $parent_obj['_id'],
                    'tag' => $tag
                )
            )
        );
      }
      else
      {
        //neither request nor reponse
        return;
      }

      $status = \iriki\engine\route::simulateRequest(
        $request_details,
        $GLOBALS['APP']
      );

      if ($status['code'] == 200)
      {
        $log_object = array(
          '_id' => $status['data'],
          'model' => $request_details['data']['params']['model'],
          'action' => $request_details['data']['params']['action'],
          'timestamp' => $timestamp,
          'parent' => $request_details['data']['params']['parent'],
          'tag' => $tag
        );

        return $log_object;
      }
    }
}

?>
