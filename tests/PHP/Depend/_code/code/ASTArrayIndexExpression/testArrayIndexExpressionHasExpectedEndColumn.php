<?php
function testArrayIndexExpressionHasExpectedEndColumn($foo, $object, $baz)
{
    echo $foo[
        $object->bar + $baz
            ]
                    ;
    echo PHP_EOL;
}

testArrayIndexExpressionHasExpectedEndColumn(array(65 => 42), (object) array('bar' => 42), 23);