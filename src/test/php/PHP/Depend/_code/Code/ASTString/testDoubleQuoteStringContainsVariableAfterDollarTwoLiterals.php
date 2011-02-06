<?php
function testDoubleQuoteStringContainsVariableAfterDollarTwoLiterals($foo)
{
    return "$$$foo";
}