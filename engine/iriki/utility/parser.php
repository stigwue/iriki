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

    public static function paginate($list, $limit, $page)
    {
        $response = array(
            'page' => (int) $page, //current page
            'total' => 0, //total pages
            'limit' => (int) $limit, //items per page
            'count' => count($list), //total items
            'data' => array() //page data
        );

        $list_count = count($list);

        $total_pages = ceil($list_count / $limit);

        $response['total'] = $total_pages;

        if ($page <= $total_pages)
        {
            $index_end = $limit * $page;
            $index_begin = ($index_end - $limit) + 1;

            if ($index_end > $list_count)
            {
                $index_end = $list_count;
            }

            $response['data'] = array_slice($list, $index_begin - 1, $index_end - $index_begin + 1);
        }

        return $response;
    }
}

?>
