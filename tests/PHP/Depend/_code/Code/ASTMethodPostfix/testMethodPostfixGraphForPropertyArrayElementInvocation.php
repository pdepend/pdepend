<?php
class testMethodPostfixGraphForPropertyArrayElementInvocation
{
    function testMethodPostfixGraphForPropertyArrayElementInvocation($bar)
    {
        $this->foo[$bar]();
    }
}