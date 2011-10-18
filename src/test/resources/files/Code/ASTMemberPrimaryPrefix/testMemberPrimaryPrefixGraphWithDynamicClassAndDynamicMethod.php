<?php
function testMemberPrimaryPrefixGraphWithDynamicClassAndDynamicMethod($class, $method)
{
    return $class::$method(23);
}
