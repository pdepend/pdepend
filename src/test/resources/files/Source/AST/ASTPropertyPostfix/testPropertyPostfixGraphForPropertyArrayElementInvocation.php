<?php
class testPropertyPostfixGraphForPropertyArrayElementInvocation
{
    function testPropertyPostfixGraphForPropertyArrayElementInvocation($bar)
    {
        $this->foo[$bar]();
    }
}
