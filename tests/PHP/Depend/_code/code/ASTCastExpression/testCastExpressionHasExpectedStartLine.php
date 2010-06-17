<?php
function testCastExpressionHasExpectedStartLine($param)
{
    return (object) $param;
}
var_dump(testCastExpressionHasExpectedStartLine(array('foo' => 'bar')));