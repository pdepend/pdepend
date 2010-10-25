<?php
function testPreIncrementExpressionHasExpectedStartColumn($param)
{
    return (++$param * ++$param);
}
var_dump(testPreIncrementExpressionHasExpectedStartColumn(1));