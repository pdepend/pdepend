<?php
class testGetNodeMetricsReturnsExpectedCboWithReturnReference
{
    /**
     * @return SplObjectStorage
     */
    public function foo()
    {
        return new SplObjectStorage();
    }
}