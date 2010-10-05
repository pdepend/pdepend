<?php
function testUnserializedFunctionStillReferencesSameDependency()
{
    return new PHP_Depend_Code_Function('foobar');
}