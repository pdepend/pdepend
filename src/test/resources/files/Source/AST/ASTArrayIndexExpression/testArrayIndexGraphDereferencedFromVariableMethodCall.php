<?php
function testArrayIndexGraphDereferencedFromVariableMethodCall($object, $method)
{
    return $object->$method()[42];
}
