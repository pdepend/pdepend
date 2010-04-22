<?php
class testNPathComplexityForThreeNestedConditionalStatements
{
    function testNPathComplexityForThreeNestedConditionalStatements()
    {
        $a = true ? ($a ? $b : ($c ? $b : $a)) : $c;
    }
}