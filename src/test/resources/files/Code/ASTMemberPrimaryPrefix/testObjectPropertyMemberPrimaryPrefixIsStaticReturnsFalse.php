<?php
function testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse($object)
{
    $object->foo = 42;
}