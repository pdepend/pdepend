<?php
class testPreIncrementExpressionOnSelfClassMember
{
    function testPreIncrementExpressionOnSelfClassMember()
    {
        return ++self::$fooBarBaz;
    }
}