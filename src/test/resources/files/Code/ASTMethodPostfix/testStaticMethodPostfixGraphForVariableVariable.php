<?php
function testStaticMethodPostfixGraphForVariableVariable($method)
{
    return MyClass::$$method();
}
