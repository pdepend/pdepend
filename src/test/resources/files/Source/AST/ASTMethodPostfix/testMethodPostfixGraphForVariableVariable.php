<?php
function testMethodPostfixGraphForVariableVariable($object, $method)
{
    return $object->$$method();
}
