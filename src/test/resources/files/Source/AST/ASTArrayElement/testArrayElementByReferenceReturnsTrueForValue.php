<?php
function testArrayElementByReferenceReturnsTrueForValue(&$param)
{
    return array(&$param, 17, 23, 42);
}
