<?php
/**
 * The return class.
 *
 * @return PHP_Depend_Return_Class
 */
function testUnserializedFunctionStillReferencesSameReturnClass()
{
    return $object;
}