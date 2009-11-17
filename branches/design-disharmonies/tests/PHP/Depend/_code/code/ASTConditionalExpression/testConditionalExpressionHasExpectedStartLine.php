<?php
function testConditionalExpressionHasExpectedStartLine()
{
    return ($foo ? 42 : 23);
}