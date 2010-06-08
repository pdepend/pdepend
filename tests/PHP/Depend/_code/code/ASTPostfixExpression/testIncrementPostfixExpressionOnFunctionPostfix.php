<?php
function testIncrementPostfixExpressionOnFunctionPostfix($param)
{
    return is_nice($param)++;
}