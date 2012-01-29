<?php
trait testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult {

    use testGetAllMethodsOnTraitUsingTraitReturnsExpectedResultUsedTrait;

    public function bar() {}
    public function baz() {}
}

trait testGetAllMethodsOnTraitUsingTraitReturnsExpectedResultUsedTrait {
    public function foo() {}
}
