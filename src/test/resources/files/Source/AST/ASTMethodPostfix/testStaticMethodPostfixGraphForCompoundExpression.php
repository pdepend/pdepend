<?php
function testStaticMethodPostfixGraphForCompoundExpression()
{
    return MyClass::{'method'}();
}
