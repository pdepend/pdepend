<?php
class testPropertyPostfixStructureForSelfVariableAccess
{
    function testPropertyPostfixStructureForSelfVariableAccess()
    {
        self::$bar;
    }
}