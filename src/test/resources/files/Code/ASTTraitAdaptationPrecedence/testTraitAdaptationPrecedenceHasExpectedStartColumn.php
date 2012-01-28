<?php
class testTraitAdaptationPrecedenceHasExpectedStartColumn
{
    use testTraitAdaptationPrecedenceHasExpectedStartColumnMyTraitOne,
        testTraitAdaptationPrecedenceHasExpectedStartColumnMyTraitTwo {
        testTraitAdaptationPrecedenceHasExpectedStartColumnMyTraitOne::foo
            insteadof
                testTraitAdaptationPrecedenceHasExpectedStartColumnMyTraitTwo;
    }
}
