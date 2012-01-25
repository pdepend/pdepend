<?php
function testArrayElementGraphSimpleValueByReference(&$foo)
{
    return array(&$foo);
}
