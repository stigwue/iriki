<?php

namespace iriki;

class response
{
    const INFO = 200;
    const ERROR = 404;

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
