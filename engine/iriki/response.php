<?php

namespace iriki;

class response
{
    //see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes

    //1xx: Informational responses

    //2xx: Success
    const OK = 200;

    //3xx: Redirection

    //4xx Client errors
    const ERROR = 400;
    //412 Precondition Failed

    //5xx Server error

    public static function showMissing($list, $description, $action)
    {
        $count_less_one = count($list) - 1;

        $singular = $plural = '';

        $message = "";

        if (is_array($description))
        {
            $singular = $description['singular'];
            $plural = $description['plural'];
        }
        else
        {
            $singular = $description;
            $plural = $singular . 's';
        }

        if ($count_less_one < 0 )
        {
            $message = "";
        }
        else
        {
            $first = $list[0];
            if ($count_less_one == 0)
            {
                //e.g 'id' parameter missing
                //or 'user' model available
                $message = "'$first' $singular $action.";
            }
            else if ($count_less_one == 1)
            {
                //e.g 'id' and 1 other parameter missing
                //or 'user' and 1 other model available
                $message = "'$first' and $count_less_one other $singular $action.";
            }
            else
            {
                //e.g 'id' and x other parameters missing
                //or 'user' and x other models available
                $message = "'$first' and $count_less_one other $plural $action.";
            }
        }

        return $message;
    }

    //build
    private static function build($code, $message = '', $data = null)
    {
      /*a response has
      code: determines data, error or info
      message: empty, error or information
      data: data
      */

      $response = array(
        'code' => $code
      );

      $response['message'] = $message;
      if (!is_null($data)) $response['data'] = $data;

      return $response;
    }

    public static function data($data, $wrap = true)
    {
        //do logging?

        if (!$wrap) return $data;
        else return Self::build(Self::OK, '', $data);
    }

    public static function information($message, $wrap = true)
    {
        if (!$wrap) return $message;
        else return Self::build(Self::OK, $message);
    }

    public static function error($message, $wrap = true)
    {
        if (!$wrap) return $message;
        else return Self::build(Self::ERROR, $message);
    }

    //log
}

?>
