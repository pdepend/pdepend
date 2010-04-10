<?php
class testMethodPostfixStructureForParentInvocation
    extends testMethodPostfixStructureForParentInvocationParent
{
    function testMethodPostfixStructureForParentInvocation()
    {
        parent::testMethodPostfixStructureForParentInvocation();
    }
}