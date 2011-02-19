<?php
interface testGetNodeMetricsReturnsExpectedCaForParameterTypes
{
    function foo(Iterator $it);

    function bar(SplObjectStorage $storage);

    function baz(ArrayAccess $objects);
}

interface testGetNodeMetricsReturnsExpectedCaForParameterTypes_interface1
{
    function foo(testGetNodeMetricsReturnsExpectedCaForParameterTypes $it);

    function bar(SplObjectStorage $storage);

    function baz(ArrayAccess $objects);
}

interface testGetNodeMetricsReturnsExpectedCaForParameterTypes_interface2
{
    function foo(Iterator $it);

    function bar(testGetNodeMetricsReturnsExpectedCaForParameterTypes $storage);

    function baz(ArrayAccess $objects);
}

interface testGetNodeMetricsReturnsExpectedCaForParameterTypes_interface3
{
    function foo(Iterator $it);

    function bar(SplObjectStorage $storage);

    function baz(testGetNodeMetricsReturnsExpectedCaForParameterTypes $objects);
}