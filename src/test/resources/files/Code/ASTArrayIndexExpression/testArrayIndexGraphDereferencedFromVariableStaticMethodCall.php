<?php
function testArrayIndexGraphDereferencedFromVariableStaticMethodCall($method)
{
    return Clazz::$method()[42];
}
