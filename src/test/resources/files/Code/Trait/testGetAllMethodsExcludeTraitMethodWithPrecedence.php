<?php
trait testGetAllMethodsHandlesTraitMethodPrecedence
{
    use testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne {
        testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitTwo::foo
            insteadof
                testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne;
    }
    use testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitTwo {
        testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne::foo
            insteadof
                testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitTwo;
    }
}

trait testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne
{
    function foo() {}
    function bar() {}
}

trait testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitTwo
{
    function foo() {}
}
