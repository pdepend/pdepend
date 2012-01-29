<?php
trait testGetAllMethodsHandlesTraitMethodPrecedence
{
    use testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne;
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
