<?php
class testTraitReference
{
    use testTraitReferenceMyTraitOne,
        testTraitReferenceMyTraitTwo {
        testTraitReferenceMyTraitOne
            /* ... */
                ::
                    // ...
                        myTraitMethod
            insteadOf
                testTraitReferenceMyTraitTwo;
    }
}
