<?php
function testCloneExpressionHasExpectedStartColumn($object)
{
    return clone
        $object->child()
            ->child();
}