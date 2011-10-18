<?php
class testMethodPostfixGraphForVariableArrayElementInvocation
{
    function testMethodPostfixGraphForVariableArrayElementInvocation(array $foo)
    {
        $this->$foo[0]();
    }
}
