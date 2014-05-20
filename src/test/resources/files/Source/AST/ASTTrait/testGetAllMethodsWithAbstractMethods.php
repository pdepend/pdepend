<?php
trait testGetAllMethodsWithAbstractMethods
{
    use testGetAllMethodsWithAbstractMethodsUsedTraitOne,
        testGetAllMethodsWithAbstractMethodsUsedTraitTwo;
}

trait testGetAllMethodsWithAbstractMethodsUsedTraitOne
{
    public function foo()
    {
    }
}

trait testGetAllMethodsWithAbstractMethodsUsedTraitTwo
{
    abstract public function foo();
}
