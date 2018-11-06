<?php

namespace iriki\engine;

/**
* Iriki parser utility.
*
*/
class parser
{
    /**
    * Converts a list to a dictionary
    *
    *
    * @param list Input list
    * @param key List property to use as key
    * @param one_to_one Mapping of key - value pairs, use one to one or otherwise
    * @return Dictionary, one to one or one to many.
    * @throw
    */
    public static function dictify($list, $key, $one_to_one = true)
    {
        $dict = array();

        foreach ($list as $item)
        {
            if (isset($item[$key]))
            {
                if ($one_to_one)
                {
                    $dict[$item[$key]] = $item;
                }
                else
                {
                    $dict[$item[$key]][] = $item;
                }
            }
        }

        return $dict;
    }
}

?>