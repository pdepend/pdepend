<?php
function testMethodPostfixOnArrayIndexedVariable()
{
    $var = array( 23 => 'var' );
    return MyClass::$var[23]();
}
