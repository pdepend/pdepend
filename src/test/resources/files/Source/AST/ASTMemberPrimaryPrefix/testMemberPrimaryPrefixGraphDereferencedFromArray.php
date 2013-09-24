<?php
function testMemberPrimaryPrefixGraphDereferencedFromArray($object, $i)
{
    isset($object->plots[0]->coords[0][$i]);
}
