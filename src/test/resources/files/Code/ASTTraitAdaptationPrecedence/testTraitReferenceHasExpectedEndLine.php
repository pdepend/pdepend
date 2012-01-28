<?php
class testTraitReferenceHasExpectedEndLine
{
    use testTraitReferenceHasExpectedEndLineMyTraitOne,
        testTraitReferenceHasExpectedEndLineMyTraitTwo {
        testTraitReferenceHasExpectedEndLineMyTraitOne
            /* ... */
                ::
                    // ...
                        myTraitMethod
            insteadOf
                testTraitReferenceHasExpectedEndLineMyTraitTwo;
    }
}
