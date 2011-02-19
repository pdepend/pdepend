<?php
class testNPathComplexityForNestedIfStatementsWithoutScope
{
    function testNPathComplexityForNestedIfStatementsWithoutScope()
    {
        if ($x)
            if ($y)
                if ($z)
                    $foo = $x * $y * $z;
    }
}