<?php
class testPropertyPostfixStructureForParentVariableAccess extends testPropertyPostfixStructureForParentVariableAccessParent
{
    public function testPropertyPostfixStructureForParentVariableAccess()
    {
        parent::$foo;
    }
}