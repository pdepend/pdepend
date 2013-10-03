<?php
/**
 * @package foo
 */
class testAnalyzerNotCountsImplementedAbstractMethodsAsOverwritten
    extends testAnalyzerNotCountsImplementedAbstractMethodsAsOverwrittenParent
{
    public function foo() {}
    public function bar() {}
}

/**
 * @package foo
 */
abstract class testAnalyzerNotCountsImplementedAbstractMethodsAsOverwrittenParent
{
    abstract public function foo();
    public function bar() {}
}
