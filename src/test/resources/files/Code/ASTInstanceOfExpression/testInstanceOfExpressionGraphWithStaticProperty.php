<?php
function testInstanceOfExpressionGraphWithStaticProperty($object)
{
    return ($object instanceof $object::$clazz);
}