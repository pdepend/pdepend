<?php
class testTraitAdaptationPrecedenceHasExpectedEndColumn
{
    use testTraitAdaptationPrecedenceHasExpectedColumnEndMyTraitOne,
        testTraitAdaptationPrecedenceHasExpectedColumnEndMyTraitTwo {
        testTraitAdaptationPrecedenceHasExpectedColumnEndMyTraitOne::foo
            insteadof
                testTraitAdaptationPrecedenceHasExpectedEndColumnMyTraitTwo;
    }
}
