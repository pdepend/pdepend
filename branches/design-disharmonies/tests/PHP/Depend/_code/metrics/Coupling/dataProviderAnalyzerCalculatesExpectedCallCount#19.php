<?php
namespace bar\baz {
    class Foo {
        public static function foobar() {}
    }
}

namespace foo {
    use bar\baz as bb;
    use bar\baz\Foo;

    function dataProviderAnalyzerCalculatesExpectedCallCount() {
        \bar\baz\Foo::foobar();
        bb\Foo::foobar();
        Foo::foobar();
    }
}