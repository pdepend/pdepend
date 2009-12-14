<?php
namespace foo {

    use bar\baz as bb;

    function dataProviderAnalyzerCalculatesExpectedCallCount18()
    {
        bb\foobar();
        \bar\baz\foobar();
    }
}