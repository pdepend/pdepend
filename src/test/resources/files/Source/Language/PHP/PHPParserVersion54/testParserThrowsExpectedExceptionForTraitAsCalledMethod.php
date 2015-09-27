<?php
function testParserThrowsExpectedExceptionForTraitAsCalledMethod($object)
{
    return $object->trait();
}
