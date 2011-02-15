<?php
class testGetNodeMetricsReturnsExpectedCeWithStaticReference
{
    public static function testGetNodeMetricsReturnsExpectedCeWithStaticReference()
    {
        return FooBar::baz();
    }
}