<?php
function testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse($object)
{
    return $object->bar();
}