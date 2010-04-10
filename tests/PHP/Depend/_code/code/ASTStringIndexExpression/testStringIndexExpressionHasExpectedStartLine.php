<?php
function testStringIndexExpressionHasExpectedStartLine($object)
{
    var_dump($object->foo{2});
}
testStringIndexExpressionHasExpectedStartLine((object) array('foo' => 'bar'));