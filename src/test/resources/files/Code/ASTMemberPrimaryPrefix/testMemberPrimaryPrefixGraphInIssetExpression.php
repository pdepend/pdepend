<?php
function testMemberPrimaryPrefixGraphInIssetExpression($object, $i)
{
    return $object->plots[0]->coords[0][$i];
}
