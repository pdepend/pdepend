<?php
class testArgumentsContainsParentMethodPostfixExpression
    extends testArgumentsContainsParentMethodPostfixExpressionParent
{
    public function testArgumentsContainsParentMethodPostfixExpression()
    {
        $this->foo(
            parent::testArgumentsContainsParentMethodPostfixExpression()
        );
    }
}