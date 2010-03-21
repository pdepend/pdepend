<?php
class testAnalyzerNotCountsImplementedInterfaceMethodsAsOverwritten
    extends testAnalyzerNotCountsImplementedInterfaceMethodsAsOverwrittenParent
{
    public function foo() {}
    public function bar() {}
    protected function baz() {}
}

abstract class testAnalyzerNotCountsImplementedInterfaceMethodsAsOverwrittenParent
    implements testAnalyzerNotCountsImplementedInterfaceMethodsAsOverwrittenInterface
{
    protected function baz() {}
}

interface testAnalyzerNotCountsImplementedInterfaceMethodsAsOverwrittenInterface
{
    public function foo();
    function bar();
}