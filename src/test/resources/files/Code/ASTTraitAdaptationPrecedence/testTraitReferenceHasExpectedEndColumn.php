<?php
class testTraitReferenceHasExpectedEndColumn
{
    use testTraitReferenceHasExpectedEndColumnMyTraitOne,
        testTraitReferenceHasExpectedEndColumnMyTraitTwo {
        testTraitReferenceHasExpectedEndColumnMyTraitOne
            /* ... */
                ::
                    // ...
                        myTraitMethod
            insteadOf
                testTraitReferenceHasExpectedEndColumnMyTraitTwo;
    }
}
