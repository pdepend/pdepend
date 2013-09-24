<?php
function testGetImageForVariableMethod($object, $method)
{
    return $object->$method(23);
}
