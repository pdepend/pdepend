<?php
class testMethodPostfixStructureForSelfInvocation
{
    function testMethodPostfixStructureForSelfInvocation()
    {
        self::bar();
    }
}