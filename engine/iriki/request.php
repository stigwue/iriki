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
    * Session token, passed around in header.
    *
    */
    private $_session_token;

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
    * Gets the session toekn
    *
    * @returns string Session token
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
    * @returns string Session token
    * @throw
    */
    public function setSession($session_token)
    {
      $this->_session_token = $session_token;
      return $this->_session_token;
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
            $new_result = \iriki\engine\response::buildFor('auth', 'User session token invalid or expired.', $wrap);
          break;

          //expecting a particular parameter which is missing
          //especially ids of parent models
          case response::ERROR:
            if ($result['message'] == 'missing_parameter')
            {
              $new_result = \iriki\engine\response::buildFor('error', 'Parameter missing or of wrong type.', $wrap);
            }
            else
            {
              //some other error
              $new_result = \iriki\engine\response::buildFor('error', 'Some other error occurred.', $wrap);
            }
          break;

          default:
            //nothing was caught?
            $new_result = \iriki\engine\response::buildFor($default_response, $result, $wrap);
          break;
        }
      }
      //if they are not set, we are returning null or some stuff, just return as is (if we are not to wrap)
      //else, we should wrap with the appropriate response markers
      else
      {
        //no errors
        $new_result = \iriki\engine\response::buildFor($default_response, $result, $wrap);
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
    * @returns object Response object or data
    * @throw
    */
    public function create($request, $wrap = true)
    {
      $db = Self::$_db_instance;

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

      $result = $db::doCreate($request);

      //intercept errors so as to display them accordingly
      $final_result = Self::catchError($result, 'information', $wrap);

      return $final_result;
    }

    /**
    * Perform a read action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @param array Data sort descriptor
    * @returns object Response object or data
    * @throw
    */
    public function read($request, $wrap = true)
    {
      $db = Self::$_db_instance;
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
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      //check sort metadata
      $meta = $request->getMeta();
      $sort = (isset($meta['sort']) ? $meta['sort'] : array());

      $result = $db::doRead($request, $sort);

      //intercept errors so as to display them accordingly
      $final_result = Self::catchError($result, 'data', $wrap);

      return $final_result;
    }

    /**
    * Perform a 'read all' action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @param array Data sort descriptor
    * @returns object Response object or data
    * @throw
    */
    public function read_all($request, $wrap = true)
    {
      $db = Self::$_db_instance;

      $parameter_status = $request->getParameterStatus();

      //replace with modified
      $request->setParameterStatus($parameter_status);

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

      $result = $db::doRead($request, $sort);

      $final_result = Self::catchError($result, 'data', $wrap);

      return $final_result;
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

      //unique
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

      $result = $db::doUpdate($request);

      $final_result = Self::catchError($result, 'information', $wrap);

      return $final_result;
    }

    /**
    * Perform a delete action on a request
    *
    * @param object Request object
    * @param boolean Wrap results with descriptors
    * @returns object Response object or data
    * @throw
    */
    public function delete($request, $wrap = true)
    {
      $db = Self::$_db_instance;

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
        return response::error(
          response::showMissing($parameter_status['extra'], 'parameter', 'extra'),
          $wrap);
      }

      $result = $db::doDelete($request);
      
      $final_result = Self::catchError($result, 'information', $wrap);

      return $final_result;
    }
}

?>
