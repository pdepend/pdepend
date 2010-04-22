<?php
class testNPathComplexityForConditionalStatementWithLogicalExpressions
{
    function testNPathComplexityForConditionalStatementWithLogicalExpressions()
    {
        $a or true ? $b && $c and $c : $d xor $e;
    }
}