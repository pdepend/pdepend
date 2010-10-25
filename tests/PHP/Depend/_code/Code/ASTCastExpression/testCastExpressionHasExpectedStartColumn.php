<?php
function testCastExpressionHasExpectedStartColumn($param)
{
    return (object) $param;
}
var_dump(testCastExpressionHasExpectedStartColumn(array('foo' => 'bar')));