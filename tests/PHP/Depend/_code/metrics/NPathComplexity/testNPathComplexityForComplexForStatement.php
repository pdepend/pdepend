<?php
class testNPathComplexityForComplexForStatement
{
    function testNPathComplexityForComplexForStatement()
    {
        for ($i = 0, $j = 42; $i < $j && $j > 23 || $j < 42; --$i, ++$j) {
        }
    }
}