<?php
class testPropertyPostfixGraphForArrayElementInvocation
{
    public function testPropertyPostfixGraphForArrayElementInvocation($foo)
    {
        return $this->$foo[0];
    }
}
