<?php
function testPreIncrementExpressionHasExpectedEndColumn($param)
{
    return (++$param * ++$param);
}
var_dump(testPreIncrementExpressionHasExpectedEndColumn(1));