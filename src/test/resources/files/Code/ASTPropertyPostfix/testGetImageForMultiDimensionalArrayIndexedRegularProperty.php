<?php
function testGetImageForMultiDimensionalArrayIndexedRegularProperty(stdClass $object)
{
    return $object->property[23]['foo'][42]['bar'];
}
