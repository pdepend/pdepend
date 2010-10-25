<?php
function testStringIndexExpressionHasExpectedEndLine($object)
{
    var_dump($object->foo{2});
}
testStringIndexExpressionHasExpectedEndLine((object) array('foo' => 'bar'));