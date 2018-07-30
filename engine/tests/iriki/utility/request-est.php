<?php

namespace iriki_tests\engine;

//replace with a better composer installed library

class requestTest extends \PHPUnit\Framework\TestCase
{
    //use guzzle?
    
	public static function doGet($url, $parameters)
    {
        $url = $url . '?';
        foreach ($parameters as $key => $value)
        {
            $url = $url . $key . '=' . $value . '&';
        }
        $result = file_get_contents($url);
        return $result;
    }

    public static function doPost($url, $parameters)
    {
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($parameters),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}
?>
