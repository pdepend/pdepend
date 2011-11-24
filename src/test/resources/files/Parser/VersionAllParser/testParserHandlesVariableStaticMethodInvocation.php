<?php
class testParserHandlesVariableStaticMethodInvocationClass
{
    function testParserHandlesVariableStaticMethodInvocation($method)
    {
        MyClass::$$method();
    }
}
