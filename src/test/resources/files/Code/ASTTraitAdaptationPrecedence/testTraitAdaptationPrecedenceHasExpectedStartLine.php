<?php
class testTraitAdaptationPrecedenceHasExpectedStartLine
{
    use testTraitAdaptationPrecedenceHasExpectedStartLineMyTraitOne,
        testTraitAdaptationPrecedenceHasExpectedStartLineMyTraitTwo {
        testTraitAdaptationPrecedenceHasExpectedStartLineMyTraitOne::foo
            insteadof
                testTraitAdaptationPrecedenceHasExpectedStartLineMyTraitTwo;
    }
}
