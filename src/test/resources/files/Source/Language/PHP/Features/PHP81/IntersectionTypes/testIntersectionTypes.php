<?php
class Foo
{
    public function bar(Iterator&\Countable&\ArrayAccess $iterator)
    {
        return $iterator;
    }
}
