<?php
namespace Foo\Bar;

use function Bar\baz, something;
use function test as t;

class Someclass
{
    function b()
    {
        baz();
        something();
        t();
    }
}
