<?php

namespace Foo\Bar {

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
}

namespace Bar\Baz {

    use Foo\Bar\Abc;

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
            new Abc;
        }
    }
}

namespace {

    use Foo\Bar\Xyz;

    class Someclass
    {
        function b()
        {
            new Bar;
            new Foo\Bar;
            new Xyz;
        }
    }
}

