<?php
class testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount
{
    /**
     * @var SplObjectStorage
     */
    protected $storage = null;

    /**
     * @var SplObjectStorage
     */
    protected $objects = null;

    protected function createNewStorage()
    {
        return new SplObjectStorage();
    }

    protected function createNewObjects()
    {
        return new ArrayObject();
    }
}