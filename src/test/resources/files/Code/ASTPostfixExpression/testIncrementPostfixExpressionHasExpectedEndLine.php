<?php
function testIncrementPostfixExpressionHasExpectedEndLine($param)
{
    return (++$param * 
        $param
        // Test
            ++
    );
}
var_dump(testIncrementPostfixExpressionHasExpectedEndLine(1));