<?php
function testGetImageForArrayIndexedVariableProperty($object)
{
    $property = 'prop';
    return $object->$property[42];
}
