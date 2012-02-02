<?php
class testGetAllMethodsOnClassWithParentReturnsTraitMethod
    extends testGetAllMethodsOnClassWithParentReturnsTraitMethodParent
{
    use testGetAllMethodsOnClassWithParentReturnsTraitMethodUsedTraitOne;
}

class testGetAllMethodsOnClassWithParentReturnsTraitMethodParent {
    public function foo() {

    }
}

trait testGetAllMethodsOnClassWithParentReturnsTraitMethodUsedTraitOne {
    public function foo() {

    }
}
