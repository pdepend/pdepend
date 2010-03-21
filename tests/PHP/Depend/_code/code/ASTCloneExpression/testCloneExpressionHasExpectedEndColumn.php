<?php
function testCloneExpressionHasExpectedEndColumn($object)
{
    return clone
        $object->child()
            ->child();
}