<?php

class Test
{
    private static $foo = [
        'bar' => 'biz',
    ];

    public static function getFoo()
    {
        return self::$foo['bar'];
    }
}
