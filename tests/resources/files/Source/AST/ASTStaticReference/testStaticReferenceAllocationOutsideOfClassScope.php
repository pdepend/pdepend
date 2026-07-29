<?php
function testStaticReferenceAllocationOutsideOfClassScope()
{
    new static();
}