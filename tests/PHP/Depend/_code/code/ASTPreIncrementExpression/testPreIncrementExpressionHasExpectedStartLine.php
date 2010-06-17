<?php
function testPreIncrementExpressionHasExpectedStartLine($param)
{
    return (++$param * ++$param);
}
var_dump(testPreIncrementExpressionHasExpectedStartLine(1));