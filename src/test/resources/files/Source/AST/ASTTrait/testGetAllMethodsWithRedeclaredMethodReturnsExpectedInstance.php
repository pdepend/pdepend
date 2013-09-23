<?php
trait testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance
{
    use testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstanceUsedTrait;

    public function foo() {

    }
}

trait testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstanceUsedTrait
{
    public function foo() {

    }
}
