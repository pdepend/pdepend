<?php
function testDoubleQuoteStringContainsVariableAfterSilenceOperator($in)
{
    return "@$in";
}