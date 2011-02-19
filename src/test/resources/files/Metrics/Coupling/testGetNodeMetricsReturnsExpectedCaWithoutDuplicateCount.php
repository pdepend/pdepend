<?php
class testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
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

class testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount_duplicate1
{
    /**
     * @var testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
     */
    protected $storage = null;

    /**
     * @var testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
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

class testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount_duplicate2
{
    /**
     * @var testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
     */
    protected $storage = null;

    /**
     * @var testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
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