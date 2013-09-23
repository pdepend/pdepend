<?php
class testParserHandlesCompoundStaticMethodInvocationClass
{
    function testParserHandlesCompoundStaticMethodInvocation()
    {
        MyClass::{'foo' . 'bar'}();
    }
}
