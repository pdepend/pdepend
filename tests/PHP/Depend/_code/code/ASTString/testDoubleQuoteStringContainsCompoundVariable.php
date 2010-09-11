<?php
function testDoubleQuoteStringContainsCompoundVariable()
{
    $input = 42;
    return "${'input'}";
}

echo testDoubleQuoteStringContainsCompoundVariable();