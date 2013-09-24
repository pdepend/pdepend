<?php
class testTraitUseStatementWithSimpleInsteadofHasExpectedEndColumn
{
    use testTraitUseStatementWithSimpleInsteadofHasExpectedEndColumnMyTraitOne,
        testTraitUseStatementWithSimpleInsteadofHasExpectedEndColumnMyTraitTwo {
            testTraitUseStatementWithSimpleInsteadofHasExpectedEndColumnMyTraitOne::foo
                insteadof
                    testTraitUseStatementWithSimpleInsteadofHasExpectedEndColumnMyTraitTwo; }
}
