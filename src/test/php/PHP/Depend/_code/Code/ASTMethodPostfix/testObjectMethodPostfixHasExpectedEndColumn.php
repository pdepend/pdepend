<?php
function testObjectMethodPostfixHasExpectedEndColumn($object)
{
    return $object
        ->
            foo(
                );
}