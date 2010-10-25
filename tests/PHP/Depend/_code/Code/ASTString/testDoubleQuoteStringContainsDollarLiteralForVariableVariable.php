<?php
function testDoubleQuoteStringContainsVariableVariable($input)
{
    $input = 'foo';
    return "$$input";
}

echo testDoubleQuoteStringContainsVariableVariable('x');