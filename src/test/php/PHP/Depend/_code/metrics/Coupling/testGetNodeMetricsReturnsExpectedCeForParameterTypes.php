<?php
interface testGetNodeMetricsReturnsExpectedCeForParameterTypes
{
    function foo(Iterator $it);

    function bar(SplObjectStorage $storage);

    function baz(ArrayAccess $objects);
}