<?php
class testIncrementPostfixExpressionOnSelfClassMember
{
    public function testIncrementPostfixExpressionOnSelfClassMember()
    {
        self::$foo++;
    }
}