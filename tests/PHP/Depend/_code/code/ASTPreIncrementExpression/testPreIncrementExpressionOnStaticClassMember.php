<?php
function testPreIncrementExpressionOnStaticClassMember()
{
    return ++stdClass::$foo;
}