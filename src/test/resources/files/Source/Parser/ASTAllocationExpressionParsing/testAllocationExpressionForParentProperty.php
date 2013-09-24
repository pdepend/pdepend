<?php
class testAllocationExpressionForParentProperty
    extends testAllocationExpressionForParentPropertyParent
{
    public static function foo()
    {
        return new parent::$x[0];
    }
}
