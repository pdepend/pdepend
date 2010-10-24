<?php
function testArrayIndexExpressionGraphForVariable(array $array)
{
    var_dump($array[42]);
}
testArrayIndexExpressionGraphForVariable(array(42 => __FILE__));