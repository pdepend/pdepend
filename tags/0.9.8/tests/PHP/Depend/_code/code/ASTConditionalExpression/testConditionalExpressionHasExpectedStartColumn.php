<?php
function testConditionalExpressionHasExpectedStartColumn()
{
    return ($foo ? 42 : 23);
}