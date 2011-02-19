<?php
function testMemberPrimaryPrefixGraphForChainedObjectMemberAccess($obj)
{
    $obj->foo->bar()->baz();
}