<?php
function testIncrementPostfixExpressionHasExpectedEndColumn($param)
{
    return (++$param * 
        $param
        // Test
            ++
    );
}
var_dump(testIncrementPostfixExpressionHasExpectedEndColumn(1));