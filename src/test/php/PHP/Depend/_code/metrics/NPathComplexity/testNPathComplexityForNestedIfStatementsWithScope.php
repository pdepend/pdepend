<?php
class testNPathComplexityForNestedIfStatementsWithScope
{
    function testNPathComplexityForNestedIfStatementsWithScope()
    {
        if ($x) {
            if ($y) {
                if ($z) {
                    $foo = $x * $y * $z;
                }
            }
        }
    }
}