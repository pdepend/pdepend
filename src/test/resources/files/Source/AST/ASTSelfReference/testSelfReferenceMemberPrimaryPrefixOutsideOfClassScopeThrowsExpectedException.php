<?php
function testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
{
    self::foo();
}