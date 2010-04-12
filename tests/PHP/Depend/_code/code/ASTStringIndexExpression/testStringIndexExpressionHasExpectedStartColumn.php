<?php
function testStringIndexExpressionHasExpectedStartColumn($object)
{
    var_dump($object->foo{2});
}
testStringIndexExpressionHasExpectedStartColumn((object) array('foo' => 'bar'));