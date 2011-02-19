<?php
class testUnserializedMethodStillReferencesSameDependencyInterface
{
    public function testUnserializedMethodStillReferencesSameDependencyInterface($o)
    {
        if ($o instanceof FooBarBazI) {
            return $o;
        }
        return null;
    }
}

interface FooBarBazI {}