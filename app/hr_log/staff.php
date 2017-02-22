<?php

namespace hr_log;

class staff extends \iriki\request
{
  public function create_one($request)
  {
    if (!is_null($request))
    {
      $request->initializedb();
      return $request->create();
    }
    else
    {
      //fail gracefully some way?
    }
  }

  public function read_by_id($request)
  {
    if (!is_null($request))
    {
      return $request->read($request);
    }
    else
    {
      //fail gracefully some way?
    }
  }

  public function read_by_email($request)
  {
    if (!is_null($request))
    {
      $request->initializedb();

      return $request->read();
    }
  }

  public function read_all($request)
  {
    if (!is_null($request))
    {
      $request->initializedb();

      $request->setData(array());

      return $request->read();
    }
  }

  public function update_one($request)
  {
    if (!is_null($request))
    {
      $request->initializedb();
      return $request->update();
    }
  }

}

?>
