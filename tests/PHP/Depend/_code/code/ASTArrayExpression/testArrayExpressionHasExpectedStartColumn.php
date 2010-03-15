<?php
function testArrayExpressionHasExpectedStartColumn($foo, $object, $baz)
{
    echo $foo[
        $object->bar + $baz
            ]
                    ;
    echo PHP_EOL;
}

testArrayExpressionHasExpectedStartColumn(array(65 => 42), (object) array('bar' => 42), 23);