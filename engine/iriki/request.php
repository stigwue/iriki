<?php

namespace iriki;

class request
{
    //see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods

    private $_db_type;//db_type

    //model_status, see array definition in route
    private $_model_status;

    /*private $_model;
    private $_action;*/

    private $_data; //data

    //others
    private static $_db_instance = null;
    //relationships
    private $_session; //session

    //build
    public static function initialize(
      $db_type,
      $model_status,
      $data = null,
      $session = null
    )
    {
      $obj = new request;
      $obj->_db_type = $db_type;
      $obj->_model_status = $model_status;
      $obj->_data = $data;
      $obj->_session = $session;

      return $obj;
    }

    public function initializedb()
    {
      if (is_null(Self::$_db_instance))
      {
        Self::$_db_instance = new $this->_db_type();

        $db_instance = &Self::$_db_instance;

        $db_instance::initialize();
      }

      return request::$_db_instance;
    }

    //properties
    public function getDBType()
    {
      return $this->_db_type;
    }

    public function setDBType($db_type)
    {
      $this->_db_type = $db_type;
      return null;
    }

    public function getModel()
    {
      return $this->_model_status['str'];
    }

    public function setModel($model)
    {
      $this->_model_status['str'] = $model;
      return null;
    }

    public function getData()
    {
      return $this->_data;
    }

    public function setData($data)
    {
      $this->_data = $data;
      return null;
    }

    //log

    //for each of the CRUD actions, do (if available) pre and post actions
    //relationship | C | R | U | D
    //belongsto    | v | x | / | x
    //hasmany      | x | v | / | v

    //note that there's a recursivity variable to limit this relationship checks

    public function create($request, $wrap = true)
    {
      $instance = $this->initializedb();

      //check unique params
      /*model::doParameterUniqueCheck(
        &$model_status,
        $final_properties,
        $request->getData(), //$final_values,
        $request
      );*/

      //check belongs to
      //each model we belong to must have a 'model+id_field' or
      //'model' field (with id_field) in request data

      $result = $instance::doCreate($request);

      if (!$wrap) return $result;
      else return \iriki\response::data($result);
    }

    public function read($request, $wrap = true)
    {
      $instance = $request->initializedb();

      //read should also pick up any "hasmany" models up to x recursivity
      $result = $instance::doRead($request);

      if (!$wrap) return $result;
      else return \iriki\response::data($result);
    }

    public function read_all($request, $wrap = true)
    {
      $instance = $request->initializedb();

      $request->setData(array());

      //read should also pick up any "hasmany" models up to x recursivity
      $result = $instance::doRead($request);

      if (!$wrap) return $result;
      else return \iriki\response::data($result);
    }

    public function update($request, $wrap = true)
    {
      $instance = $this->initializedb();

      $result = $instance::doUpdate($request);

      if (!$wrap) return $result;
      else return \iriki\response::information($result);
    }

    public function delete($request, $wrap = true)
    {
      $instance = $this->initializedb();

      $result = $instance::doDelete($request);

      if (!$wrap) return $result;
      else return \iriki\response::information($result);
    }

    public function other()
    {
      //log

      //return
    }
}

?>
