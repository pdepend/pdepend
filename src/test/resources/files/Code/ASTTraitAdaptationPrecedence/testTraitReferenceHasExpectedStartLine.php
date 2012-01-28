<?php
class testTraitReferenceHasExpectedStartLine
{
    use testTraitReferenceHasExpectedStartLineMyTraitOne,
        testTraitReferenceHasExpectedStartLineMyTraitTwo {
        testTraitReferenceHasExpectedStartLineMyTraitOne
            /* ... */
                ::
                    // ...
                        myTraitMethod
            insteadOf
                testTraitReferenceHasExpectedStartLineMyTraitTwo;
    }
}
