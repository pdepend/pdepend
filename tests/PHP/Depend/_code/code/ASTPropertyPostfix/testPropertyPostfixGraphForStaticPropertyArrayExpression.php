<?php
class testPropertyPostfixGraphForStaticPropertyArrayExpression
{
    private static $arguments = array();

    public function testPropertyPostfixGraphForStaticPropertyArrayExpression()
    {
        self::$arguments[42] = func_get_args();
    }
}