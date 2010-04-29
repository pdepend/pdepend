<?php
function testMemberPrimaryPrefixGraphForObjectPropertyAccess($obj)
{
    var_dump($obj->foo);
}
testMemberPrimaryPrefixGraphForObjectPropertyAccess((object) array('foo' => 23));