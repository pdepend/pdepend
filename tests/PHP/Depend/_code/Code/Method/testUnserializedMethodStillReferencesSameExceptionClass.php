<?php
class testUnserializedMethodStillReferencesSameExceptionClass
{
    /**
     * @throws \RuntimeException
     */
    function testUnserializedMethodStillReferencesSameExceptionClass()
    {
        throw new RuntimeException();
    }
}