<?php
function testObjectMethodPostfixHasExpectedStartLine($object)
{
    return $object
        ->
            foo(
                );
}