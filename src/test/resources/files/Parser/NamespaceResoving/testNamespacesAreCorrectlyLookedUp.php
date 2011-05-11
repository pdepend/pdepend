<?php

namespace Foo\Bar;

use Bar\Baz, Something;
use Test as T;
use Baz\Foo;

class Someclass
{
    function b()
    {
        new Bar;
        new Baz;
        new Something;
        new T;
        new Other;
        new Foo\Bar;
    }
}

