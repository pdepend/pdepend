<?php
function testStaticReferenceMemberPrimaryPrefixOutsideOfClassScope()
{
    static::foo();
}