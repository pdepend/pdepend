<?php
class testTraitReferenceHasExpectedStartColumn
{
    use testTraitReferenceHasExpectedStartColumnMyTraitOne,
        testTraitReferenceHasExpectedStartColumnMyTraitTwo
    {
        testTraitReferenceHasExpectedStartColumnMyTraitOne::myTraitMethod as foo;
    }
}
