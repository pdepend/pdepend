<?php
class testGetNodeMetricsReturnsExpectedCeForParentTypeReference
    extends testGetNodeMetricsReturnsExpectedCeForParentTypeReference_parent
{
    public function bar()
    {
        return testGetNodeMetricsReturnsExpectedCeForParentTypeReference_parent::foo();
    }
}

class testGetNodeMetricsReturnsExpectedCeForParentTypeReference_parent
{
    public static function foo()
    {

    }
}