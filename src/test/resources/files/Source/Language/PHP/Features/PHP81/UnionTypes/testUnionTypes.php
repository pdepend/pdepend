<?php
class Foo
{
    public function bar(array|int|float|Bar\Biz|null $number)
    {
        return $number * 4;
    }
}
