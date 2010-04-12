<?php
class testPropertyPostfixGraphForStaticPropertyArrayIndexExpression
{
    private static $arguments = array();

    public function testPropertyPostfixGraphForStaticPropertyArrayIndexExpression()
    {
        self::$arguments[42] = func_get_args();
    }
}