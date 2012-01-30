<?php
class testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod
    extends testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethodParent
{
    use testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethodUsedTraitOne {
        testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethodParent::foo
            insteadof
                testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethodUsedTraitOne;
    }
}

class testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethodParent
{
    public function foo() {

    }
}

trait testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethodUsedTraitOne {
    public function foo() {

    }
}
