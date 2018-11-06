<?php

namespace iriki_tests\engine;

class parserTest extends \PHPUnit\Framework\TestCase
{
    public function test_dictify_one_to_one_success()
    {
        $list = array(
            array(
                'some_key' => 'some_value',
                'other_key' => 'other_value',
            ),
            array(
                'some_key' => 'similar_value',
                'other_key' => 'related_value',
            )
        );

        $dict = \iriki\engine\parser::dictify($list, 'some_key', true);

        //var_dump($dict);

        $this->assertEquals(true,
            (count($dict) == count($list)) AND
            ($list[0] == $dict[$list[0]['some_key']])
        );
    }
}
?>
