<?php
function testDoubleQuoteStringContainsCompoundExpressionAfterLiteral($surname)
{
    return "Manuel{$surname}";
}