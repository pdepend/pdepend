<?php
class testGetNodeMetricsReturnsExpectedCaWithReturnReference
{
    /**
     * @return SplObjectStorage
     */
    public function foo()
    {
        return new SplObjectStorage();
    }
}

class testGetNodeMetricsReturnsExpectedCaWithReturnReference_return
{
    /**
     * @return SplObjectStorage
     */
    public function testGetNodeMetricsReturnsExpectedCaWithReturnReference_return()
    {
        return testGetNodeMetricsReturnsExpectedCaWithReturnReference::foo();
    }
}