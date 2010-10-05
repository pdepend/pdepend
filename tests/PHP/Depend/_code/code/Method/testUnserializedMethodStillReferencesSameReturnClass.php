<?php
class testUnserializedMethodStillReferencesSameReturnClass
{
    /**
     * @return FooBarBaz42
     */
    public function testUnserializedMethodStillReferencesSameReturnClass()
    {
        return new FooBarBaz42();
    }
}