<?php
class testGetNodeMetricsReturnsExpectedCaWithStaticReference
{
    public static function baz()
    {
        return 42;
    }
}

class testGetNodeMetricsReturnsExpectedCaWithStaticReference_static
{
    public static function testGetNodeMetricsReturnsExpectedCaWithStaticReference_static()
    {
        return testGetNodeMetricsReturnsExpectedCaWithStaticReference::baz();
    }
}