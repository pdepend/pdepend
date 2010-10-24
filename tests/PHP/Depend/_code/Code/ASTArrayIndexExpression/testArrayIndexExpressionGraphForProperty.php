<?php
function testArrayIndexExpressionGraphForProperty($object)
{
    var_dump($object->foo[42]);
}
testArrayIndexExpressionGraphForProperty((object) array('foo' => array(42 => 'X')));