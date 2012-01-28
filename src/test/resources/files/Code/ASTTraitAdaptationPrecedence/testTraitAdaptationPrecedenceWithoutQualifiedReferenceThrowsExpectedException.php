<?php
class testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedException
{
    use testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedExceptionMyTraitOne,
        testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedExceptionMyTraitTwo {

        foo insteadOf testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedExceptionMyTraitTwo;
    }
}
