<?php
function testGetImageForVariableStaticMethod($method)
{
    return Clazz::$method(42);
}
