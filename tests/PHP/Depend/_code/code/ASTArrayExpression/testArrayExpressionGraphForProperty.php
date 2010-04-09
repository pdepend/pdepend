<?php
function testArrayExpressionGraphForProperty($object)
{
    var_dump($object->foo[42]);
}
testArrayExpressionGraphForProperty((object) array('foo' => array(42 => 'X')));