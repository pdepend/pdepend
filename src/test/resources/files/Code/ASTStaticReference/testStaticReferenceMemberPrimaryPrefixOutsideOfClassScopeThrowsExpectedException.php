<?php
function testStaticReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
{
    static::foo();
}