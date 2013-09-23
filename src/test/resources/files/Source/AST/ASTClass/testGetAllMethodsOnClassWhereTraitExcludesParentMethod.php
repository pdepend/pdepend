<?php
class testGetAllMethodsOnClassWhereTraitExcludesParentMethod
    extends testGetAllMethodsOnClassWhereTraitExcludesParentMethodParent
{
    use testGetAllMethodsOnClassWhereTraitExcludesParentMethodUsedTraitOne;/* {
        testGetAllMethodsOnClassWhereTraitExcludesParentMethodUsedTraitOne::foo
            InsteadOF
                testGetAllMethodsOnClassWhereTraitExcludesParentMethodParent;
    }*/
}

class testGetAllMethodsOnClassWhereTraitExcludesParentMethodParent
{
    public function foo() { echo __METHOD__, PHP_EOL; }
}

trait testGetAllMethodsOnClassWhereTraitExcludesParentMethodUsedTraitOne
{
    public function foo() { echo __METHOD__, PHP_EOL; }
}

$object = new testGetAllMethodsOnClassWhereTraitExcludesParentMethod();
$object->foo();
