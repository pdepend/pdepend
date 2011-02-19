<?php
interface BCollection
{
    function equals($object);
    function add($object);
}

interface BList extends BCollection
{
    function get($index);
}

abstract class AbstractList implements BList
{
    public function add($object) {}
    public function get($index) {}
    public function equals($object) {}
}

class ArrayList extends AbstractList
{
    public function add($object) {}
    public function get($index) {}
}

class Order
{
    /**
     *
     * @var BList
     */
    protected $lineItems = null;
}