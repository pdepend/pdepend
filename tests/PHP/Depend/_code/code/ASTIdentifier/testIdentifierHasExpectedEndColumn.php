<?php
function testIdentifierHasExpectedEndColumn(SplObjectStorage $storage)
{
    return $storage->contains($storage);
}