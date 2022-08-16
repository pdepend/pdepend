<?php
class Foo
{
    public function bar(Iterator&\Countable $iterator)
    {
        return $iterator;
    }
}
