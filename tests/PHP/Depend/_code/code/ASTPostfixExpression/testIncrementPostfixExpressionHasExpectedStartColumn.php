<?php
function testIncrementPostfixExpressionHasExpectedStartColumn($param)
{
    return (++$param * 
        $param
        // Test
            ++
    );
}
var_dump(testIncrementPostfixExpressionHasExpectedStartColumn(1));