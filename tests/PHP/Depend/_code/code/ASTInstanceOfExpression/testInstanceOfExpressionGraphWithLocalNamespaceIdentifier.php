<?php
function testInstanceOfExpressionGraphWithLocalNamespaceIdentifier($object)
{
    return ($object instanceof foo\bar\Baz);
}