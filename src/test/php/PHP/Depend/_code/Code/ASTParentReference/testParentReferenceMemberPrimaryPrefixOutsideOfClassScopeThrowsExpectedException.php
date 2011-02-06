<?php
function testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
{
    parent::foo();
}