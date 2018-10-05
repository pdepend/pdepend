<?php
function testListExpressionWithKeysAndNestedList()
{
    list('a' => $a, 'b' => list($b, list('c' => $c, 'd' => $d))) = array(
        'a' => 'a',
        'b' => array(
            'b',
            array(
                'c' => 'c',
                'd' => 'd',
            ),
        ),
    );
    var_dump($a, $b, $c, $d);
}
