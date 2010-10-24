<?php
function testPreIncrementExpressionHasExpectedEndLine($param)
{
    return (++$param * ++$param);
}
var_dump(testPreIncrementExpressionHasExpectedEndLine(1));