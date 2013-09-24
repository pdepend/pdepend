<?php
function testStaticMethodPostfixGraphForCompoundVariable()
{
    return MyClass::${'method'}();
}
