<?php
class testNPathComplexityForTwoNestedConditionalStatements
{
    function testNPathComplexityForTwoNestedConditionalStatements()
    {
        $a = true ? $a ? $b : $c : $c;
    }
}