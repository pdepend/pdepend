<?php
class testGetNodeMetricsReturnsExpectedCboForParentTypeReference
    extends testGetNodeMetricsReturnsExpectedCboForParentTypeReference_parent
{
    public function bar()
    {
        return testGetNodeMetricsReturnsExpectedCboForParentTypeReference_parent::foo();
    }
}

class testGetNodeMetricsReturnsExpectedCboForParentTypeReference_parent
{
    public static function foo()
    {

    }
}