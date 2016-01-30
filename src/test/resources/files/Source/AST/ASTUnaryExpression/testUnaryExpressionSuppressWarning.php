<?php
function testUnaryExpressionSuppressWarning(\SplObjectStorage $storage)
{
    return @$storage->offsetGet('Sindelfingen');
}
