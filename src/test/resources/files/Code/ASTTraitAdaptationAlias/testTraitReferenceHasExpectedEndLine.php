<?php
class testTraitReferenceHasExpectedEndLine
{
    use testTraitReferenceHasExpectedEndLineMyTraitOne,
        testTraitReferenceHasExpectedEndLineMyTraitTwo
    {
        testTraitReferenceHasExpectedEndLineMyTraitOne::myTraitMethod as foo;
    }
}
