<?php
function testMemberPrimaryPrefixGraphForObjectWithVariablePropertyAccess($object, $property)
{
    $object->$property = 23;
}
