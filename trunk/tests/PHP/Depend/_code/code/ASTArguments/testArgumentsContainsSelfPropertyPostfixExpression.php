<?php
class testArgumentsContainsSelfPropertyPrimaryExpression
{
    function testArgumentsContainsSelfPropertyPrimaryExpression()
    {
        test::bar(self::$bar);
    }
}