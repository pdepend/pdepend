<?php
function testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
{
    new self();
}