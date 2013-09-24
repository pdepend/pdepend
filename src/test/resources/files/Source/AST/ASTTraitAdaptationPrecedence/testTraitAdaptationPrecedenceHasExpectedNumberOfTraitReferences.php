<?php
class testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferences
{
    use testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferencesMyTraitOne,
        testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferencesMyTraitTwo,
        testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferencesMyTraitThree {

        testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferencesMyTraitOne::foo
            insteadof
                testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferencesMyTraitTwo,
                testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferencesMyTraitThree;
    }
}
