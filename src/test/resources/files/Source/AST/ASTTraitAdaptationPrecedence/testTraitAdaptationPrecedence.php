<?php
class testTraitAdaptationPrecedence
{
    use testTraitAdaptationPrecedenceHasExpectedColumnEndMyTraitOne,
        testTraitAdaptationPrecedenceHasExpectedColumnEndMyTraitTwo {
        testTraitAdaptationPrecedenceHasExpectedColumnEndMyTraitOne::foo
            insteadof
                testTraitAdaptationPrecedenceMyTraitTwo;
    }
}
