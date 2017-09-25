<?php

namespace iriki;

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
    * Full namespace of database object.
    * There is no default set here so without setting, initializedb will return null
    *
    * @var string
    */
    private $_db_type = '';

    /**
    * Internal database handler instance.
    *
    * @var object
    */
    private static $_db_instance = null;

    /**
    * Internal database handler instance.
    *
    * @var object
    */
    private $_session_token; //user_session token

    /**
    * Model and action status, see definition in route::matchUrl
    *
    * @var array
    */
    private $_model_status;

    /**
    * Parameters expected by request in their states.
    *
    * @var array
    */
    private $_parameter_status;

    /**
    * Data to be handled by request. Just an associative array.
    *
    * @var array
    */
    private $_data;

    /**
    * Metadata to be used by request.
    * This is an associative array for things like sorting.
    *
    * @var array
    */
    private $_meta;

    /**
    * Gets the internal database instace
    *
    *
    * @return object Internal database instance object.
    * @throw
    */
    public static function getDBInstance()
    {
      return Self::$_db_instance;
    }

    /**
    * Gets the full namespace of database object
    *
    *
    * @return string Database namespace
    * @throw
    */
    public function getDBType()
    {
      return $this->_db_type;
    }

    /**
    * Sets the full namespace of database object
    *
    * @param string Database namespace
    * @return string Database namespace
    * @throw
    */
    public function setDBType($db_type)
    {
      $this->_db_type = $db_type;
      return $this->_db_type;
    }

    /**
    * Gets the model status
    *
    * @return array Model status
    * @throw
    */
    public function getModelStatus()
    {
      return $this->_model_status;
    }

    /**
    * Sets the model status
    *
    * @param array Model status
    * @return array Model status
    * @throw
    */
    public function setModelStatus($model_status)
    {
      $this->_model_status = $model_status;
      return $this->_model_status;
    }

    /**
    * Gets the parameter status/states
    *
    * @return array Parameter status
    * @throw
    */
    public function getParameterStatus()
    {
      return $this->_parameter_status;
    }

    /**
    * Sets the parameter status/states
    *
    * @param array Parameter status
    * @return array Parameter status
    * @throw
    */
    public function setParameterStatus($parameter_status)
    {
      $this->_parameter_status = $parameter_status;
      return $this->_parameter_status;
    }

    /**
    * Gets the request model
    *
    * @return string Request model
    * @throw
    */
    public function getModel()
    {
      return $this->_model_status['str'];
    }

    /**
    * Sets the request model
    *
    * @param string Request model
    * @return string Request model
    * @throw
    */
    public function setModel($model)
    {
      $this->_model_status['str'] = $model;
      return $this->_model_status['str'];
    }

    /**
    * Gets the request data
    *
    * @return array Request data
    * @throw
    */
    public function getData()
    {
      return $this->_data;
    }

    /**
    * Sets the request data
    *
    * @param array Request data
    * @return array Request data
    * @throw
    */
    public function setData($data)
    {
      $this->_data = $data;
      return $this->_data;
    }

    /**
    * Gets the request metadata
    *
    * @return array Request metadata
    * @throw
    */
    public function getMeta()
    {
      return $this->_meta;
    }

    /**
    * Sets the request metadata
    *
    * @param array Request metadata
    * @return array Request metadata
    * @throw
    */
    public function setMeta($meta)
    {
      $this->_meta = $meta;
      return $this->_meta;
    }

    /**
    * Gets the session toekn
    *
    * @return string Session token
    * @throw
    */
    public function getSession()
    {
      return $this->_session_token;
    }

    /**
    * Sets the Session token
    *
    * @param string Session token
    * @return string Session token
    * @throw
    */
    public function setSession($session_token)
    {
      $this->_session_token = $session_token;
      return $this->_session_token;
    }

    /**
    * Initialize the internal database instance
    * This has not been passed as an object?
    * Because the database class has mostly static functions.
    *
    *
    * @return object Database instance object.
    * @throw
    */
    public function initializedb()
    {
      //if uninitialized, do initialization
      if (is_null(Self::$_db_instance) AND class_exists($this->_db_type))
      {
        $db_type = $this->_db_type;
        Self::$_db_instance = new $db_type();

        $db_instance = &Self::$_db_instance;

        return request::$_db_instance;
      }
      else
      {
        return null;
      }
    }

    /**
     * Intercepts authentication or some other last minute errors reported.
     * Note that code is only set if an error occurs (code is then an error code) or wrapping is specified.
     *
     * @param {string} result Result returned from database operation
     * @param {string} default_response Response type to use if all goes well (data, information or error)
     * @param {boolean} wrap Flag to perform a response wrap or not
     * @returns {array} Final result
     */
    public static function catchError($result, $default_response, $wrap)
    {
      $new_result = $result;
      
      //if code and message are set,
      //most probably we are looking at an error
      if (isset($result['code']) && isset($result['message']))
      {
        //something error'd
        switch ($result['code'])
        {
          //authentication failed
          case response::AUTH:
            $new_result = \iriki\response::buildFor('auth', 'User session token invalid or expired.', $wrap);
          break;

          //expecting a particular parameter which is missing
          //especially ids of parent models
          case response::ERROR:
            if ($result['message'] == 'missing_parameter')
            {
              $new_result = \iriki\response::buildFor('error', 'Parameter missing or of wrong type.', $wrap);
            }
            else
            {
              //some other error
            }
          break;

          default:
            //nothing was caught?
          break;
        }
      }
      //if they are not set, we are returning null or some stuff, just return as is (if we are not to wrap)
      //else, we should wrap with the appropriate response markers
      else
      {
        //no errors
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
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @return object Response object or data
    * @throw
    */
    public function create($request, $wrap = true)
    {
      $instance = $this->initializedb();

      //unique
      $matching = model::doParameterUniqueCheck($request);
      if (count($matching) != 0)
      {
          $result = array();

          if (!$wrap) return $result;
          else return response::error(
            response::showMissing($matching, 'parameter', 'mismatched'),
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
            return response::error(
              response::showMissing($parameter_status['missing'], 'relationship parameter', 'missing'),
              $wrap);
        }
        if ($extra_parameters != 0) return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      //hasmany

      $result = $instance::doCreate($request);

      //intercept errors so as to display them accordingly
      $final_result = Self::catchError($result, 'information', $wrap);

      return $final_result;
      /*return \iriki\response::information($result['message'], $wrap, $result['data']);*/
    }

    /**
    * Perform a read action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @param array Data sort descriptor
    * @return object Response object or data
    * @throw
    */
    public function read($request, $wrap = true)
    {
      $instance = $this->initializedb();

      //unique
      //belongsto
      //hasmany
      $parameter_status = model::doHasManyRelation($request);
      //read should pick up any "hasmany" models up to x recursivity

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      //check sort metadata
      $meta = $request->getMeta();
      $sort = (isset($meta['sort']) ? $meta['sort'] : array());

      $result = $instance::doRead($request, $sort);

      //intercept errors so as to display them accordingly
      $final_result = Self::catchError($result, 'data', $wrap);

      return $final_result;

      //intercept auth or some other last minute error
      /*if (isset($result['code']) && isset($result['message']))
      {
        if ($result['code'] == response::AUTH && $result['message'] == 'unauthorized')
        {
          if ($wrap)
          {
            return \iriki\response::auth('User session token invalid or expired.');
          }
          else
          {
            return array();
          }
        }
      }

      return \iriki\response::data($result, $wrap);*/
    }

    /**
    * Perform a 'read all' action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @param array Data sort descriptor
    * @return object Response object or data
    * @throw
    */
    public function read_all($request, $wrap = true)
    {
      $instance = $this->initializedb();

      $parameter_status = $request->getParameterStatus();

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $request->setData(array());

      //check sort metadata
      $meta = $request->getMeta();
      $sort = (isset($meta['sort']) ? $meta['sort'] : array());

      //read should also pick up any "hasmany" models up to x recursivity
      $result = $instance::doRead($request, $sort);

      //intercept auth error
      if (isset($result['code']) && isset($result['message']))
      {
        if ($result['code'] == response::AUTH && $result['message'] == 'unauthorized')
        {
          if ($wrap)
          {
            return \iriki\response::auth(
              'User session token invalid or expired.',
              $wrap);
          }
          else
          {
            return array();
          }
        }
      }

      return \iriki\response::data($result, $wrap);
    }

    /**
    * Perform an update action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @return object Response object or data
    * @throw
    */
    public function update($request, $wrap = true)
    {
      $instance = $this->initializedb();

      //unique
      //belongsto
      $parameter_status = model::doBelongsToRelation($request);

      $missing_parameters = count($parameter_status['missing']);
      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0 OR $missing_parameters != 0)
      {
        if ($missing_parameters != 0)
        {
            return response::error(
              response::showMissing($parameter_status['missing'], 'relationship parameter', 'missing'),
              $wrap);
        }
        if ($extra_parameters != 0) return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }
      //hasmany

      $result = $instance::doUpdate($request);

      //intercept auth error
      if (isset($result['code']) && isset($result['message']))
      {
        if ($result['code'] == response::AUTH && $result['message'] == 'unauthorized')
        {
          if ($wrap)
          {
            return \iriki\response::auth(
              'User session token invalid or expired.',
              $wrap);
          }
          else
          {
            return array();
          }
        }
      }

      return \iriki\response::information($result, $wrap);
    }

    /**
    * Perform a delete action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @return object Response object or data
    * @throw
    */
    public function delete($request, $wrap = true)
    {
      $instance = $this->initializedb();

      //unique
      //belongsto
      //hasmany
      $parameter_status = model::doHasManyRelation($request);
      //delete should affect any "hasmany" models, ignoring recursivity limits

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $result = $instance::doDelete($request);

      //intercept auth error
      if (isset($result['code']) && isset($result['message']))
      {
        if ($result['code'] == response::AUTH && $result['message'] == 'unauthorized')
        {
          if ($wrap)
          {
            return \iriki\response::auth(
              'User session token invalid or expired.',
              $wrap);
          }
          else
          {
            return array();
          }
        }
      }

      return \iriki\response::information($result, $wrap);
    }
}

?>
