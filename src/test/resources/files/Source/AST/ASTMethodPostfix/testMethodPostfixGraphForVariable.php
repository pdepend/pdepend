<?php
function testMethodPostfixOnVariableClassProperty()
{
    $var = 'var';
    return MyClass::$var();
}
