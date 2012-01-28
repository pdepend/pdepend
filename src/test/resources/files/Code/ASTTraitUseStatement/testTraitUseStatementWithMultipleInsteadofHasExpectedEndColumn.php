<?php
class testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn
{
    use testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitOne,
        testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitTwo,
        testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitThree {
            testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitOne::foo
                insteadof
                    testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitTwo,
                    testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitThree; }
}
