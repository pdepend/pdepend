<?php
function testPreDecrementExpressionOnStaticVariableMember($obj, $prop)
{
    return --$obj::$prop;
}