<?php

use bar\baz as bb;
use bar\baz\Foo;

class foo {
    function bar() {
        \bar\baz\Foo::foobar();
        bb\Foo::foobar();
        Foo::foobar();
    }
}

