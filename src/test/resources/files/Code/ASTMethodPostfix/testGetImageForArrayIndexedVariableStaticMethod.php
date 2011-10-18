<?php
function testGetImageForArrayIndexedVariableStaticMethod($method)
{
    return Clazz::$method[42](23);
}
