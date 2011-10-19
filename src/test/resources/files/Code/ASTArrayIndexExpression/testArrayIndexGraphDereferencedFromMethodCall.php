<?php
function testArrayIndexGraphDereferencedFromMethodCall($object)
{
    return $object->method()[23];
}
