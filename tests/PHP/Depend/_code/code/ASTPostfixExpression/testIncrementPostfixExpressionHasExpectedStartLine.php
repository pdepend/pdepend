<?php
function testIncrementPostfixExpressionHasExpectedStartLine($param)
{
    return (++$param * 
        $param
        // Test
            ++
    );
}
var_dump(testIncrementPostfixExpressionHasExpectedStartLine(1));