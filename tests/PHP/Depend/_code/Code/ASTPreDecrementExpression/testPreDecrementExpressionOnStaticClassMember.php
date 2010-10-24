<?php
function testPreDecrementExpressionOnStaticClassMember()
{
    return --stdClass::$foo;
}