<?php
function testMemberPrimaryPrefixGraphForObjectWithVariableMethodAccess($object, $method)
{
    $object->$method(23, 42);
}
