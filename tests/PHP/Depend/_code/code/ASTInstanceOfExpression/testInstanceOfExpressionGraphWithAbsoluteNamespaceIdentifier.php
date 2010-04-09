<?php
function testInstanceOfExpressionGraphWithAbsoluteNamespaceIdentifier($object)
{
    return ($object instanceof \foo\bar\Baz);
}