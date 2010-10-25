<?php
function testCastExpressionHasExpectedEndColumn($param)
{
    return (object) $param;
}
var_dump(testCastExpressionHasExpectedEndColumn(array('foo' => 'bar')));