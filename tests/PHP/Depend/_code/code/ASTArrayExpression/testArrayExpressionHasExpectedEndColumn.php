<?php
function testArrayExpressionHasExpectedEndColumn($foo, $object, $baz)
{
    echo $foo[
        $object->bar + $baz
            ]
                    ;
    echo PHP_EOL;
}

testArrayExpressionHasExpectedEndColumn(array(65 => 42), (object) array('bar' => 42), 23);