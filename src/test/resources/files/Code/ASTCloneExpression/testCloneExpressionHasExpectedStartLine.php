<?php
function testCloneExpressionHasExpectedStartLine($object)
{
    return clone
        $object->child()
            ->child();
}