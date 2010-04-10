<?php
function testStringIndexExpressionHasExpectedEndColumn($object)
{
    var_dump($object->foo{2});
}
testStringIndexExpressionHasExpectedEndColumn((object) array('foo' => 'bar'));