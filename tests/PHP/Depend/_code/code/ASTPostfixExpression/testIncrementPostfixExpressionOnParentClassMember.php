<?php
class testIncrementPostfixExpressionOnParentClassMember extends stdClass
{
    public function testIncrementPostfixExpressionOnParentClassMember()
    {
        parent::$foo++;
    }
}