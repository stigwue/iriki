<?php

namespace iriki;

class stat_request
{
  //structure
  /*
  _id
  timestamp
  request_count
  models (model => request_count, action ( => request_count))
  actions (action => request_count)
  */

  public static function initialize($timestamp)
  {
    $stat = array(
      'timestamp' => $timestamp,
      'request_count' => 0,
      'models' => array(
        /*
          'model' => array(
            'request_count' => 0,
            'action' => 0
          )
        */
      ),
      'actions' => array(
        /*
          'action' => 0
        */
      )
    );

    return $stat;
  }

  //first of all, the last stat added to the collection is updated by requests
  //so, during fixed intervals, a new one is added
  //ideally, this should be called by cron, except that cron might not know the db_type

  public static function moveHead($timestamp)
  {
    $request = request::initialize(
      '\iriki\engine\mongodb',//db_type
      'iriki_stat_request', //model
      'create', //action
      Self::initialize($timestamp) //data
    );

    $request->create($request, false);
  }
}

?>
