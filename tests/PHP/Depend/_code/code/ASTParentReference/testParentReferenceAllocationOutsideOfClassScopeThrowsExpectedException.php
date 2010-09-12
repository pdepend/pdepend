<?php
function testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
{
    new parent();
}