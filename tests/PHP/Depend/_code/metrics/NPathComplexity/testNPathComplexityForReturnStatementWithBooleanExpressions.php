<?php
class testNPathComplexityForReturnStatementWithBooleanExpressions
{
    function testNPathComplexityForReturnStatementWithBooleanExpressions()
    {
        return true && false || bar;
    }
}