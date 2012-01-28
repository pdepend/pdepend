<?php
class testTraitReferenceHasExpectedStartColumn
{
    use testTraitReferenceHasExpectedStartColumnMyTraitOne,
        testTraitReferenceHasExpectedStartColumnMyTraitTwo {
        testTraitReferenceHasExpectedStartColumnMyTraitOne
            /* ... */
                ::
                    // ...
                        myTraitMethod
            insteadOf
                testTraitReferenceHasExpectedStartColumnMyTraitTwo;
    }
}
