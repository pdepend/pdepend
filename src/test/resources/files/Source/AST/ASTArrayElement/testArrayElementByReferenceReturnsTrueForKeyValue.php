<?php
function testArrayElementByReferenceReturnsTrueForKeyValue($param)
{
    return array(42 => &$param);
}
