<?php
function testIncrementPostfixExpressionOnStaticMethodPostfix()
{
    return stdClass::foo()++;
}