<?php
class testAnalyzerReturnsExpectedClassLocFromCache
{
    public $foo;

    protected function bar()
    {

    }

    private  function baz()
    {
        if (true)
        {
            return 42;
        }
        return 23;
    }
}
