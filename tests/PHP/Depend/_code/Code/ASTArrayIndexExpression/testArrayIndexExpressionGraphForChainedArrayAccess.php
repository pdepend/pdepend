<?php
function testArrayIndexExpressionGraphForChainedArrayAccess($array)
{
    var_dump($array[0][0][0]);
}
testArrayIndexExpressionGraphForChainedArrayAccess(array(array(array('FooBar'))));