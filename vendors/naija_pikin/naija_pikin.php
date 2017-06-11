<?php

//A hopefully funny and non-annoying way to pick random names for unregistered users.

//Basically, it combines a noun (a Nigerian name - names pulled from faker.ng: https://github.com/binkabir/faker.ng) and an adjective (pulled from http://adjectivesthatstart.com/).

//I have not gone thorugh the adjectives to remove offensive ones!

class NaijaPikin
{ 
    private $_json;

	//read json file
    private static function load_json_file($json_path)
    {
        $json = null;
        try {
            $contents = file_get_contents($json_path);
            $json = json_decode($contents, TRUE);
        }
        catch (Exception $e) {
            //load default json
        }

        return $json;
    }

	//load json file
	public function loadDictionary($path)
	{
		$_json = NaijaPikin::load_json_file($path);
	}

	//pick noun

	//pick adjective
}

?>