<?php
class testGetAllMethodsWithMethodCollisionThrowsExpectedException
{
    use testGetAllMethodsWithMethodCollisionThrowsExpectedExceptionUsedTraitOne,
        testGetAllMethodsWithMethodCollisionThrowsExpectedExceptionUsedTraitTwo;
}

trait testGetAllMethodsWithMethodCollisionThrowsExpectedExceptionUsedTraitOne
{
    public function foo() {}
}

trait testGetAllMethodsWithMethodCollisionThrowsExpectedExceptionUsedTraitTwo
{
    public function foo() {}
}
