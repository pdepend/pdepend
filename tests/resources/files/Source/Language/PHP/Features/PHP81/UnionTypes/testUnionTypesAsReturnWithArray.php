<?php
class Foo
{
    public function bar(): Countable | iterable
    {
        return [];
    }
}
