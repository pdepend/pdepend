<?php
class Foo
{
    public function bar(): Iterator&\Countable&\ArrayAccess
    {
        return new ArrayIterator();
    }
}
