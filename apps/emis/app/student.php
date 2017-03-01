<?php

namespace emis;

class student extends \iriki\request
{
  public function create_one($request)
  {
    if (!is_null($request))
    {
      return $request->create($request);
    }
    else
    {
      //fail gracefully some way?
    }
  }

  public function read_all($request)
  {
    if (!is_null($request))
    {
      $request->setData(array());

      return $request->read($request);
    }
  }
}
