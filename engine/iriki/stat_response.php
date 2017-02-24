<?php

namespace iriki;

class stat_response
{
  /*
    _id
    timestamp
    count
    models (model => count, response ( => count))
    response (error, data, info)

  */

  public static function initialize($timestamp)
  {
    $stat = array(
      'timestamp' => $timestamp,
      'response_count' => 0,
      'models' => array(
        /*
          'model' => array(
            'response_count' => 0,
            'response' => 0
          )
        */
      ),
      'response' => array(
        /*
          'response' => 0
        */
      )
    );

    return $stat;
  }
}

?>
