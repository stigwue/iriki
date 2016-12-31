<?php

namespace iriki;

class response
{
    const INFO = 200;
    const ERROR = 404;

    public static function data($data)
    {
        $response = array();
        $response['data'] = $data;

        //do logging?

        return $response;
    }

    public static function information($message)
    {
        $response = array();
        $response['info'] = array(
            'code' => Self::INFO,
            'message' => $message
        );

        //do logging?

        return $response;
    }


    public static function error($message)
    {
        $response = array();
        $response['error'] = array(
            'code' => Self::ERROR,
            'message' => $message
        );

        //do logging?

        return $response;
    }
}

?>
