<?php
function testStaticReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
{
    new static();
}