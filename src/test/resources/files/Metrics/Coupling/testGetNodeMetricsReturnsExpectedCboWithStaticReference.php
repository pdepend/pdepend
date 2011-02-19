<?php
class testGetNodeMetricsReturnsExpectedCboWithStaticReference
{
    public static function testGetNodeMetricsReturnsExpectedCboWithStaticReference()
    {
        return FooBar::baz();
    }
}