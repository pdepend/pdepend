<?php
function testArrayExpressionGraphForChainedArrayAccess($array)
{
    var_dump($array[0][0][0]);
}
testArrayExpressionGraphForChainedArrayAccess(array(array(array('FooBar'))));