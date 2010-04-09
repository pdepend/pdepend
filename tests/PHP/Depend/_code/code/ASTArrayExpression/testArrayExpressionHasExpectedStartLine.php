<?php
function testArrayExpressionHasExpectedStartLine($foo, $object, $baz)
{
    echo $foo[
        $object->bar + $baz
            ]
                    ;
    echo PHP_EOL;
}

testArrayExpressionHasExpectedStartLine(array(65 => 42), (object) array('bar' => 42), 23);