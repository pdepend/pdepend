<?php
function testIdentifierHasExpectedEndLine(SplObjectStorage $storage)
{
    return $storage->contains($storage);
}