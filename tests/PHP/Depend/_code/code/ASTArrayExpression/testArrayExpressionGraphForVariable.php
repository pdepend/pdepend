<?php
function testArrayExpressionGraphForVariable(array $array)
{
    var_dump($array[42]);
}
testArrayExpressionGraphForVariable(array(42 => __FILE__));