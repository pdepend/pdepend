<?php
function testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue()
{
    return stdClass::$foo;
}