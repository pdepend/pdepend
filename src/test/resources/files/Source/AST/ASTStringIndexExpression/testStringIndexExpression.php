<?php
function testStringIndexExpression($object)
{
    var_dump($object->foo{2});
}
testStringIndexExpression((object) array('foo' => 'bar'));
