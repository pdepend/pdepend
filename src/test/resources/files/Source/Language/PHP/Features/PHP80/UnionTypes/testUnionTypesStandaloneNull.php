<?php
class Foo
{
    public function bar(null $number)
    {
        return $number ?? 4;
    }
}
