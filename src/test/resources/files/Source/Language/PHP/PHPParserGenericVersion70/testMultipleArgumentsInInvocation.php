<?php

class testMultipleArgumentsInInvocation
{
    public static function execute()
    {
        return static::getInvokable()('Hello', $a);
    }

    private static function getInvokable() {
        return new class {
            public function __invoke($arg1, $arg2) {
                echo $arg1 . ' ' . $arg2 . PHP_EOL;
            }
        };
    }
}
