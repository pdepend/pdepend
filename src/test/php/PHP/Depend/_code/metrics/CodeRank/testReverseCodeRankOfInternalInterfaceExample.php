<?php
interface BList extends Iterator
{
    function equals($object);
    function add($object);
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
     * @var BList
     */
    public $lineItems;
}