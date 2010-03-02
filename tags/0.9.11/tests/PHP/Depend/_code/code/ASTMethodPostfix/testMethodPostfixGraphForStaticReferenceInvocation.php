<?php
class testMethodPostfixGraphForStaticReferenceInvocation
{
    function testMethodPostfixGraphForStaticReferenceInvocation()
    {
        static::foo();
    }
}