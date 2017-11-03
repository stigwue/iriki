<?php

namespace iriki;

/**
* Iriki response.
*
*/
class response
{
    //see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes

    //1xx: Informational responses

    /**
    * Success responses
    * HTTP 2xx: Success
    *
    * @var integer
    */
    const OK = 200;

    //3xx: Redirection

    /**
    * Error response
    * HTTP 4xx Client errors
    *
    * @var integer
    */
    const ERROR = 400;

    /**
    * Unauthorized error response
    * HTTP 4xx Client errors
    *
    * @var integer
    */
    const AUTH = 401;
    //412 Precondition Failed

    //5xx Server error

    /**
    * Build a message explaining an action.
    *
    *
    * @param {array} Array of items
    * @param {array} Singular and plural (keys) name of items
    * @param {array} Singular and plural (keys) name of items
    * @returns Message string
    * @throw
    */
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

    /**
    * Build a response object
    *
    *
    * @param integer Reponse code
    * @param string Response message, typically user understandable
    * @param object Response data, mostly arrays or strings
    * @returns Response object
    * @throw
    */
    public static function build($code, $message = '', $data = null)
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

    /**
    * Build a response by specifiying response type
    *
    *
    * @param {string} response_type Response type: data, information or error.
    * @param {array} Request result
    * @param {boolean} Wrap response or not?
    * @returns Response object
    */
    public static function buildFor($response_type, $result, $wrap)
    {
        //already supplied: response_type for code

        //result? check if code is set in result
        //if yes, result is pre-wrapped
        $pre_wrapped = isset($result['code']) || isset($result['message']) || isset($result['data']);

        //wrap? if yes, pass as is, changing code maybe
        if ($wrap)
        {
            $response = array();

            switch (strtolower($response_type))
            {
                case 'auth':
                    $response['code'] = response::AUTH;
                case 'error':
                    $response['code'] = response::ERROR;

                    //message
                    if ($pre_wrapped)
                    {
                        $response = Self::build(
                            $response['code'],
                            $result['message'],
                            isset($result['data']) ? $result['data'] : null
                        );
                    }
                    else
                    {
                        //errors don't have any data component
                        $response = Self::build($response['code'], $result);
                    }
                break;

                case 'data':
                    $response['code'] = response::OK;

                    //data
                    if ($pre_wrapped)
                    {
                        $response = Self::data(
                            $result['data'],
                            true,
                            (isset($result['message']) ? $result['message'] : '')
                        );
                    }
                    else
                    {
                        $response = Self::data(
                            //data
                            $result,
                            //wrap
                            true
                            //message
                        );
                    }
                break;
                
                case 'information':
                default:
                    $response['code'] = response::OK;

                    //message
                    if ($pre_wrapped)
                    {
                        $response = Self::build(
                            $response['code'],
                            $result['message'],
                            isset($result['data']) ? $result['data'] : null
                        );
                    }
                    else
                    {
                        $response = Self::build($response['code'], $result);
                    }
                break;
            }

            return $response;
        }
        else
        {
            //if no, strip out code
            //then return message or data based on response_type
            if (isset($result['code'])) unset($result['code']);
            $response = null;

            switch (strtolower($response_type))
            {
                case 'auth':
                case 'error':
                    //message
                    if ($pre_wrapped) $response = $result['message'];
                    else $response = $result;
                break;

                case 'data':
                    //data
                    if ($pre_wrapped) $response = $result['data'];
                    else $response = $result;
                break;
                
                case 'information':
                default:
                    //message
                    if ($pre_wrapped) $response = $result['message'];
                    else $response = $result;
                break;
            }

            return $response;
        }
    }

    /**
    * Build and return a data response object
    *
    *
    * @param object Response data, mostly arrays or strings
    * @param boolean Wrap data to give an Iriki Response or just raw data. Default is true.
    * @param string Response message. Default is ''.
    * @param object Model relations. Null for now.
    * @returns Response object
    * @throw
    */
    public static function data($data, $wrap = true, $message = '', $relations = null)
    {
        //do logging?

        if (!$wrap) return $data;
        else return Self::build(Self::OK, $message, $data);
    }

    /**
    * Build and return an information response object
    *
    *
    * @param string Response message.
    * @param boolean Wrap data to give an Iriki Response or just raw message. Default is true.
    * @param object Response data, mostly arrays or strings. Default is null.
    * @returns Response object
    * @throw
    */
    public static function information($message, $wrap = true, $data = null)
    {
        if (!$wrap) return $message;
        else return Self::build(Self::OK, $message, $data);
    }

    /**
    * Build and return an error response object
    *
    *
    * @param {string} Response message.
    * @param {boolean} Wrap data to give an Iriki Response or just raw message. Default is true.
    * @param {object} Response data, mostly arrays or strings. Default is null.
    * @returns {object} Response object
    * @throw
    */
    public static function error($message, $wrap = true, $data = null)
    {
        if (!$wrap) return $message;
        else return Self::build(Self::ERROR, $message, $data);
    }

    /**
    * Build and return an authentication response object
    *
    *
    * @param string Response message.
    * @param boolean Wrap data to give an Iriki Response or just raw message. Default is true.
    * @param object Response data, mostly arrays or strings. Default is null.
    * @returns Response object
    * @throw
    */
    public static function auth($message, $wrap = true, $data = null)
    {
        if (!$wrap) return $message;
        else return Self::build(Self::AUTH, $message, $data);
    }

    //log
}

?>
