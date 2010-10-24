<?php
class testUnserializedMethodStillReferencesSameDependency
{
    public function testUnserializedMethodStillReferencesSameDependency()
    {
        return new FooBarBaz42();
    }
}