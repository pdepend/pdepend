<?php
function testGetImageForMultiArrayIndexedVariableStaticMethod($method)
{
    return Clazz::$method[42][17][23]();
}
