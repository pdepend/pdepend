<?php
class testTraitReferenceHasExpectedStartLine
{
    use testTraitReferenceHasExpectedStartLineMyTraitOne,
        testTraitReferenceHasExpectedStartLineMyTraitTwo
    {
        testTraitReferenceHasExpectedStartLineMyTraitOne::myTraitMethod as foo;
    }
}
