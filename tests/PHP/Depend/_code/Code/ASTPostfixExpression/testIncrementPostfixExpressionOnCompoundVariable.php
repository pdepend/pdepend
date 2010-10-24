<?php
function testIncrementPostfixExpressionOnCompoundVariable()
{
    return ${T_QAFOO}++;
}