<?php
function testIncrementPostfixExpressionOnObjectMethodPostfix($obj)
{
    return $obj->foo($obj)++;
}