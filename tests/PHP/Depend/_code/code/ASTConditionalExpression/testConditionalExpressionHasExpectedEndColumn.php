<?php
function testConditionalExpressionHasExpectedEndColumn()
{
    return ($foo ? 42 : 23);
}