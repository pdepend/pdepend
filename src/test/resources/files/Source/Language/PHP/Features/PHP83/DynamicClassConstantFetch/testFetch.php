<?php

class Foo
{
    const BAR = 'bar';

    public static function get(string $bar): string
    {
        return self::{$bar};
    }
}
