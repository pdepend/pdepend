<?php
function testIssetExpressionGraphWithMultipleVariables()
{
    return isset($foo, $bar, $baz);
}