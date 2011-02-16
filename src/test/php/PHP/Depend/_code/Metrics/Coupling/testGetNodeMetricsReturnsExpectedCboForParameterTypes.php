<?php
interface testGetNodeMetricsReturnsExpectedCboForParameterTypes
{
    function foo(Iterator $it);

    function bar(SplObjectStorage $storage);

    function baz(ArrayAccess $objects);
}