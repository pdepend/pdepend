<?php
class Foo
{
    public function bar(int|float $number)
    {
        return $number * 4;
    }
}
