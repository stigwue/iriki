<?php

namespace iriki;

class request
{
    //see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods

    private $_db_type;//db_type
    private $_model; //model
    private $_action;
    private $_data;//data

    //others
    private static $_db_instance = null;
    private $_session; //session

    //build
    public static function initialize(
      $db_type,
      $model,
      $action,
      $data = null,
      $session = null
    )
    {
      $obj = new request;
      $obj->_db_type = $db_type;
      $obj->_model = $model;
      $obj->_action = $action;
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
    public function getModel()
    {
      return $this->_model;
    }

    public function setModel($model)
    {
      $this->_model = $model;
      return null;
    }

    public function setData($data)
    {
      $this->_data = $data;
      return null;
    }

    public function getData()
    {
      return $this->_data;
    }

    //log

    public function create()
    {
      $instance = $this->initializedb();

      $result = $instance::doCreate($this);

      return \iriki\response::data($result);
    }

    public function read($request)
    {
      $instance = $request->initializedb();

      $result = $instance::doRead($request);

      return \iriki\response::data($result);
    }

    public function update()
    {
      $instance = $this->initializedb();

      $result = $instance::doUpdate($this);

      return \iriki\response::information($result);
    }

    public function delete()
    {
      $instance = $this->initializedb();

      $result = $instance::doDelete($this);

      return \iriki\response::information($result);
    }

    public function other()
    {
      //log

      //return
    }
}

?>
