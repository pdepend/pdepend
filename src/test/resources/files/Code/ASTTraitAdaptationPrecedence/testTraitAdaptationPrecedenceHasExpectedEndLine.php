<?php
class testTraitAdaptationPrecedenceHasExpectedEndLine
{
    use testTraitAdaptationPrecedenceHasExpectedStartEndMyTraitOne,
        testTraitAdaptationPrecedenceHasExpectedStartEndMyTraitTwo {
        testTraitAdaptationPrecedenceHasExpectedStartEndMyTraitOne::foo
            insteadof
                testTraitAdaptationPrecedenceHasExpectedEndLineMyTraitTwo;
    }
}
