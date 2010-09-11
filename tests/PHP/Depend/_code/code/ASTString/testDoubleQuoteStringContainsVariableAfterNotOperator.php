<?php
function testDoubleQuoteStringContainsVariableAfterNotOperator($in)
{
    return "!$in";
}