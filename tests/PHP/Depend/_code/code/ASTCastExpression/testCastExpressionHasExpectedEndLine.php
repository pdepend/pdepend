<?php
function testCastExpressionHasExpectedEndLine($param)
{
    return (object) $param;
}
var_dump(testCastExpressionHasExpectedEndLine(array('foo' => 'bar')));