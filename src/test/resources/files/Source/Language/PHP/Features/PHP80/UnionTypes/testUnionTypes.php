<?php
class Foo
{
    public function bar(int|float|Bar\Biz|null $number)
    {
        return $number * 4;
    }
}
