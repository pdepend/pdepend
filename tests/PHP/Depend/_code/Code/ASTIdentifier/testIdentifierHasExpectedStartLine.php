<?php
function testIdentifierHasExpectedStartLine(SplObjectStorage $storage)
{
    return $storage->contains($storage);
}