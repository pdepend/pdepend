<?php
function testUnserializedFunctionStillReferencesSameDependencyInterface($object)
{
    if ($object instanceof FooBar)
    {
        return null;
    }
    return $object;
}

interface FooBar
{
    
}