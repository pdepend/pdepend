<?php
function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticMethod($class)
{
    return $class::method(42);
}
