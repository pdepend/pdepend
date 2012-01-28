<?php
class testTraitUseStatementWithSimpleInsteadofHasExpectedEndLine
{
    use testTraitUseStatementWithSimpleInsteadofHasExpectedEndLineMyTraitOne,
        testTraitUseStatementWithSimpleInsteadofHasExpectedEndLineMyTraitTwo {
            testTraitUseStatementWithSimpleInsteadofHasExpectedEndLineMyTraitOne::foo
                insteadof
                    testTraitUseStatementWithSimpleInsteadofHasExpectedEndLineMyTraitTwo;
    }
}
