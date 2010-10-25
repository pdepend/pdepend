<?php
function testIssetExpressionGraphWithArrayProperty($object)
{
    return isset($object->foo[42]);
}

var_dump(testIssetExpressionGraphWithArrayProperty((object) array('foo' => array(42 => 1))));