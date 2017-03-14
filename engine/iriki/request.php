<?php

namespace iriki;

class request
{
    //see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods

    //db_type
    //i've set the default here, otherwise an error is thrown for create in initializedb
    private $_db_type = '\iriki\engine\mongodb';

    //model_status, see array definition in route
    private $_model_status;
    private $_parameter_status;

    private $_data; //data

    //others
    private static $_db_instance = null;
    //relationships
    private $_session; //session

    //build
    public static function initialize(
      $db_type,
      $model_status,
      $parameter_status,
      $data = null,
      $session = null
    )
    {
      $obj = new request;
      $obj->_db_type = $db_type;
      $obj->_model_status = $model_status;
      $obj->_parameter_status = $parameter_status;
      $obj->_data = $data;
      $obj->_session = $session;

      return $obj;
    }

    public function initializedb()
    {
      if (is_null(Self::$_db_instance))
      {
        $db_type = $this->_db_type;
        //$db_type() throws 'Class name must be a valid object or a string' for only create inherited from request
        //nixed after I supplied a default for db_type
        Self::$_db_instance = new $db_type();

        $db_instance = &Self::$_db_instance;

        $db_instance::initialize();
      }

      return request::$_db_instance;
    }

    //properties
    public static function getDBInstance()
    {
      return Self::$_db_instance;
    }

    public function getDBType()
    {
      return $this->_db_type;
    }

    public function setDBType($db_type)
    {
      $this->_db_type = $db_type;
      return $this->_db_type;
    }

    public function getModelStatus()
    {
      return $this->_model_status;
    }

    public function setModelStatus($model_status)
    {
      $this->_model_status = $model_status;
      return $this->_model_status;
    }

    public function getParameterStatus()
    {
      return $this->_parameter_status;
    }

    public function setParameterStatus($parameter_status)
    {
      $this->_parameter_status = $parameter_status;
      return $this->_parameter_status;
    }

    public function getModel()
    {
      return $this->_model_status['str'];
    }

    public function setModel($model)
    {
      $this->_model_status['str'] = $model;
      return $this->_model_status['str'];
    }

    public function getData()
    {
      return $this->_data;
    }

    public function setData($data)
    {
      $this->_data = $data;
      return $this->_data;
    }

    //log

    //before a request action is called, request data is filled
    //and the keys (parameters) are in one of three groups: final, missing, extra
    //before relationship checks are done, parent model properties are in extra
    //a relationship check should move valid ones from extra into final or insert into missing
    //a model action is not performed until missing and extra is zero
    //--"fax!" quot the penguin

    //for each of the CRUD actions, do (if available) pre and post actions/checks
    //relationship | C | R | U | D
    //unique       | v | x | x | x
    //belongsto    | v | x | v | x
    //hasmany      | x | v | x | v

    //note that there's a recursivity variable to limit relationship checks


    public function create($request, $wrap = true)
    {
      $instance = $this->initializedb();

      //unique
      $matching = model::doParameterUniqueCheck($request);
      if (count($matching) != 0)
      {
          $result = array();

          if (!$wrap) return $result;
          else return response::error(response::showMissing($matching, 'parameter', 'mismatched'));
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
            return response::error(response::showMissing($parameter_status['missing'], 'relationship parameter', 'missing'), $wrap);
        }
        if ($extra_parameters != 0) return response::error(response::showMissing($parameter_status['extra'], 'parameter', 'extra'), $wrap);
      }

      //hasmany

      $result = $instance::doCreate($request);

      return \iriki\response::information($result, $wrap);
    }

    public function read($request, $wrap = true)
    {
      $instance = $request->initializedb();

      //unique
      //belongsto
      //hasmany
      $parameter_status = model::doHasManyRelation($request);
      //read should pick up any "hasmany" models up to x recursivity

      $extra_parameters = count($parameter_status['extra']);

      if ($extra_parameters != 0)
      {
        return response::error(response::showMissing($parameter_status['extra'], 'parameter', 'extra'), $wrap);
      }

      $result = $instance::doRead($request);

      return \iriki\response::data($result, $wrap);
    }

    public function read_all($request, $wrap = true)
    {
      $instance = $request->initializedb();

      $request->setData(array());

      //read should also pick up any "hasmany" models up to x recursivity
      $result = $instance::doRead($request);

      return \iriki\response::data($result, $wrap);
    }

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
            return response::error(response::showMissing($parameter_status['missing'], 'relationship parameter', 'missing'), $wrap);
        }
        if ($extra_parameters != 0) return response::error(response::showMissing($parameter_status['extra'], 'parameter', 'extra'), $wrap);
      }
      //hasmany

      $result = $instance::doUpdate($request);

      return \iriki\response::information($result, $wrap);
    }

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
        return response::error(response::showMissing($parameter_status['extra'], 'parameter', 'extra'), $wrap);
      }

      $result = $instance::doDelete($request);

      return \iriki\response::information($result, $wrap);
    }
}

?>
