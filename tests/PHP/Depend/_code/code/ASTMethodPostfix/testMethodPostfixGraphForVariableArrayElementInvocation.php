<?php
class testMethodPostfixGraphForArrayElementInvocation
{
    function testMethodPostfixGraphForArrayElementInvocation(array $foo)
    {
        $this->$foo[0]();
    }
}