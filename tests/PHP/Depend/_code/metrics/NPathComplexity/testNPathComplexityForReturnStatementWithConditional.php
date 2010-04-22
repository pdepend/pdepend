<?php
class testNPathComplexityForReturnStatementWithConditional
{
    function testNPathComplexityForReturnStatementWithConditional()
    {
        if (true) {
            return $a ?: 2;
        }
    }
}