<?php
class Foo
{
    public function bar()
    {
        $foo = '-';

        return number_format(5623, thousands_separator: $foo);
    }
}
