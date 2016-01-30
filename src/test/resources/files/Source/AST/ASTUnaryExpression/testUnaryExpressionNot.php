<?php
function testUnaryExpressionNot(\SplObjectStorage $storage)
{
    return !$storage->contains(
        $storage
    );
}
