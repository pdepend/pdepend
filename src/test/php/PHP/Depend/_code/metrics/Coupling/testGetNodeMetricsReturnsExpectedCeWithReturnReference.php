<?php
class testGetNodeMetricsReturnsExpectedCeWithReturnReference
{
    /**
     * @return SplObjectStorage
     */
    public function foo()
    {
        return new SplObjectStorage();
    }
}