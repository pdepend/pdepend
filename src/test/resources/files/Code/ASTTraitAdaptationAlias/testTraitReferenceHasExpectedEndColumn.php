<?php
class testTraitReferenceHasExpectedEndColumn
{
    use testTraitReferenceHasExpectedEndColumnMyTraitOne,
        testTraitReferenceHasExpectedEndColumnMyTraitTwo
    {
        testTraitReferenceHasExpectedEndColumnMyTraitOne::myTraitMethod as foo;
    }
}
