<?php

namespace iriki;

class config
{
    private $_json;
    private $_key_values;

    private static function load_json_file($json_path)
    {
        $json = '{}';
        try {
            $contents = file_get_contents($json_path);
            $json = json_decode($contents, TRUE);
        }
        catch (Exception $e) {
            //load default json
        }

        //test for null, result of malformed json
        if (is_null($json)) $json = '{}';
        return $json;
    }

    function __construct($json_path = '')
    {
        if (strlen($json_path) != 0)
        {
            $this->_json = Self::load_json_file($json_path);
        }
    }

    public function getJson()
    {
        return $this->_json;
    }

    public function getKeyValues()
    {
        //return key-value pairs
        $this->_key_values = $this->_json['iriki']['app'];
        return $this->_key_values;
    }

    public function getStatus($status = null, $json = false)
    {
        //unset some private ones?
        if (is_null($status))
        {
            $status = array('data' => array());
        }

        $status['data']['base_url'] = $this->_key_values['base_url'];

        //application
        $context = "application";
        $status['data']['description'] = $this->_key_values[$context]['description'];
        $status['data']['author'] = $this->_key_values[$context]['author'];
        $status['data']['version'] = array(
            'major' => $this->_key_values[$context]['version']['major'],
            'minor' => $this->_key_values[$context]['version']['minor'],
            'build' => $this->_key_values[$context]['version']['build']
        );
        //engine

        if ($json)
        {
            return json_encode($status);
        }
        else
        {
            return $status;
        }
    }
}
?>
