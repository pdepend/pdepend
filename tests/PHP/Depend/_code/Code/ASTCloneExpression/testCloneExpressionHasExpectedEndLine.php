<?php
function testCloneExpressionHasExpectedEndLine($object)
{
    return clone
        $object->child()
            ->child();
}