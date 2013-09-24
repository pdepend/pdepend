<?php
function testGetImageForArrayIndexedRegularProperty(stdClass $object)
{
    return $object->property[1];
}
