<?php
function testGetImageForVariableProperty($object, $property)
{
    return $object->$property;
}
