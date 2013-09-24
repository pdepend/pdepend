<?php
class testTraitReference
{
    use testTraitReferenceMyTraitOne,
        testTraitReferenceMyTraitTwo
    {
        testTraitReferenceMyTraitOne::myTraitMethod as foo;
    }
}
