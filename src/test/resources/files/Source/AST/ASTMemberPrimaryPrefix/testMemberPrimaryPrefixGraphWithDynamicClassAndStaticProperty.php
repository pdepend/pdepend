<?php
function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticProperty($class, $property)
{
    return $class::$property;
}
