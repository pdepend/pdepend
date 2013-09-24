<?php
class testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine
{
    use testTraitUseStatementWithMultipleInsteadofHasExpectedEndLineMyTraitOne,
        testTraitUseStatementWithMultipleInsteadofHasExpectedEndLineMyTraitTwo,
            testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitThree {
            testTraitUseStatementWithMultipleInsteadofHasExpectedEndLineMyTraitOne::foo
                insteadof
                    testTraitUseStatementWithMultipleInsteadofHasExpectedEndLineMyTraitTwo,
                    testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumnMyTraitThree;
    }
}
