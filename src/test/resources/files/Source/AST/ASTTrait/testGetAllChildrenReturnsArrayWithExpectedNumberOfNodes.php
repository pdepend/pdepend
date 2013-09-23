<?php
trait testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
{
    protected $foo = 23;

    protected $bar = 42;

    public function baz()
    {
        return $this->foo * $this->bar;
    }
}
