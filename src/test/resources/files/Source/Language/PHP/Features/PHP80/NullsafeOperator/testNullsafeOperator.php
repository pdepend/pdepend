<?php
class Foo
{
    public function bar($obj)
    {
        return $obj?->prop?->method()?->result;
    }
}
