<?php
function testUnserializedFunctionStillReferencesSameParameterClass(ReflectionClass $class)
{
    return $class->getName();
}