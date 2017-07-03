<?php

namespace iriki;

/**
* Iriki request statistics.
* We have used a structure which works best with MongoDB.
* So, the plan to have this stat depend on the application's db type has been nixed.
* The DB Instance is now passed to log requests.
*
*/
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
  private static $_collection = null;

  //db is an array of server and db
  public static function initialize($db, $timestamp)
  {
    if (isset($db['server']) AND isset($db['db'])) {
      $_db = new \MongoClient($db['server']);

      Self::$_collection = $_db->$db['db'];

      return Self::$_collection;
    }
    else {
      return null;
    }

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

  public static function log($request, $timestamp = 0, $move_head = false)
  {

  }

}

?>
