<?php
function testUnserializedFunctionStillReferencesSameDependency()
{
    return new MyFunction('foobar');
}
