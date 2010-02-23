<?php
use \foo\bar as fb;

function testInstanceOfExpressionGraphWithAliasedNamespaceIdentifier($object)
{
    return ($object instanceof fb\Baz);
}