<?php
function testArrayIndexExpressionHasExpectedStartLine($foo, $object, $baz)
{
    echo $foo[
        $object->bar + $baz
            ]
                    ;
    echo PHP_EOL;
}

testArrayIndexExpressionHasExpectedStartLine(array(65 => 42), (object) array('bar' => 42), 23);