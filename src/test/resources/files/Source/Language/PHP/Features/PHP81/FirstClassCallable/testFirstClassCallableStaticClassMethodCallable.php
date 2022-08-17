<?php
class Foo
{
    public function bar()
    {
        return [\Countable::class, 'method'](...);
    }
}
