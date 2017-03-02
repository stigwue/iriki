<?php

namespace emis;

class staff extends \iriki\request
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
}
