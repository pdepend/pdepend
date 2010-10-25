<?php
function testPreIncrementExpressionOnStaticVariableMember($obj, $prop)
{
    return ++$obj::$prop;
}