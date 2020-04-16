<?php

namespace iriki\engine;

use GuzzleHttp\Client;

/**
* Iriki URI/URL utility.
*
*/
class url
{
    private static $_http_client = null;

    /**
    * Parses url to pull out models, action and queries.
    *
    *
    * @param path Request url/path.
    * @return Path, parts of the url and the query
    * @throw
    */
    public static function parseUrl($path)
    {
        //try php's parse_url
        $parsed = parse_url($path);

        $to_parse = $parsed['path'];

        //path should not start with /
        if (strlen($to_parse) != 0 AND $to_parse[0] == '/')
        {
            $to_parse = substr($to_parse, 1);
        }
        
        $query = '';

        if (isset($parsed['query']))
        {
            $query = $parsed['query'];
        }

        //split path
        $parts = explode("/", $to_parse);
        //clear empties
        $parts = array_filter($parts);
        //reset index
        $model_action = array();
        $parameters = array();

        $count = 0;
        foreach ($parts as $part)
        {
            $model_action[] = $part;
            $count += 1;

            if ($count > 2) $parameters[] = $part;
        }

        return compact(
            'path', //model/action/and/all/words/after
            'parts', //array of model/action/and/all/words/after
            'parameters', //array of all/words/after
            'query' //?other_key=value_pairs
        );
    }

    /**
    * Read GET parameters from query part of the url.
    *
    *
    * @param query Query section of url
    * @return Key-value query pairs
    * @throw
    */
    public static function parseGetParams($query)
    {
        $get_params = array();
        if (strlen($query) != 0) 
        {
            $key_values = explode("&", $query);
            foreach ($key_values as $key_value)
            {
                $pair = explode('=', $key_value);
                if (count($pair) == 2)
                {
                    //property=value
                    $get_params[$pair[0]] = $pair[1];
                }
                else
                {
                    //property=value=corrupted
                    $get_params[$key_value] = '';
                }
            }
        }
        return $get_params;
    }

    /**
    * Gets the HTTP request details: methods, parameters and so forth.
    *
    *
    * @param uri URI supplied or deduced.
    * @param base_url Optional base url if framework isn't run from server root/home. Default is ''.
    * @return Request details for use.
    * @throw
    */
    public static function getRequestDetails($uri = null, $base_url = '')
    {
        if (is_null($uri))
        {
            $uri = $_SERVER['REQUEST_URI'];
        }

        if ($base_url != '')
        {
            //trim uri by base

            //optional step
            //if you are running this framework from
            //foobar.com/*iriki* then ignore
            //or else, if running from foobar.com/some/weird/path/*iriki* then
            //shorten url by /some/weird/path
            $uri = substr($uri, strlen($base_url));
        }

        $status = array(
            'url' => Self::parseUrl($uri),
            'params' => array() /*[ 'method' => ['key1' => 'value1', 'key1' => 'value1']]*/
        );

        //parameters are of http methods which correspond to CRUD
        //CRUD => POST, GET, PUT, DELETE
        //we do not know which this route uses so we query all
        //we also save the request's method incase we need to use 'ANY'

        $status['params']['ANY'] = $_SERVER['REQUEST_METHOD'];

        $status['params']['POST'] = $_POST;

        $status['params']['GET'] = (isset($status['url']['query'])) ? Self::parseGetParams($status['url']['query']) : array();

        $status['params']['PUT'] = array(); //parse_str(file_get_contents('php://input'), $data);

        $status['params']['DELETE'] = array(); //parse_str(file_get_contents('php://input'), $data);
        
        $status['params']['REQUEST'] = $_REQUEST;

        //files may have been sent too, look for them and add them
        //parameters with the same name as files will be replaced
        if (!empty($_FILES))
        {
            $params = array();
            foreach ($_FILES as $file_param => $file_details)
            {
                $params[$file_param] = $file_details;
            }
            $status['params']['FILE'] = $params;
            
        }

        return $status;
    }

    public static function makeRequest($url, $params = array(), $method = "POST", $headers = array(), $json = false)
    {
        $response = null;

        $method = strtolower($method);

        $params_key = '';

        switch ($method)
        {
            //create: post
            case 'post':
                $params_key = 'form_params';
            break;

            /*
            //update: put
            case 'put':
            break;

            //delete: delete
            case 'delete':
            break;
            */

            //read
            default: //GET
                $params_key = 'query';
            break;
        }

        if ($json) $params_key = 'json';


        try
        {
            Self::$_http_client = new Client();

            $response = Self::$_http_client->request(
                $method,
                $url,
                [
                    $params_key => $params,
                    'headers' => $headers
                    //'auth'
                ]
            );
        }
        catch (\GuzzleHttp\Exception\RequestException $e)
        {
            $response = null;

        }
        catch (\GuzzleHttp\Exception\ConnectException $e)
        {
            $response = null;
        }
        catch (\GuzzleHttp\Exception\ClientException $e)
        {
            $response = null;
        }
        catch (\GuzzleHttp\Exception\ServerException $e)
        {
            $response = null;
        }
        catch (\GuzzleHttp\Exception\TooManyRedirectsException $e)
        {
            $response = null;
        }
        catch (Exception $e)
        {
            $response = null;
        }

        if (is_null($response))
        {
            return '{}';
        }
        else
        {
            return (string) $response->getBody();
        }
    }
}

?>
