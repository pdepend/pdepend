<?php
function testObjectMethodPostfixHasExpectedEndLine($object)
{
    return $object
        ->
            foo(
                );
}