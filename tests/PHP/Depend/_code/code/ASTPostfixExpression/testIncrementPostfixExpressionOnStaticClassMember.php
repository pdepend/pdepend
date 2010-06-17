<?php
class testIncrementPostfixExpressionOnStaticClassMember
{
    public function testIncrementPostfixExpressionOnStaticClassMember()
    {
        stdClass::$foo++;
    }
}