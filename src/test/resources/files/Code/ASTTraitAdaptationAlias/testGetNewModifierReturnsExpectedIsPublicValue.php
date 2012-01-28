<?php
class testGetNewModifierReturnsExpectedIsPublicValue
{
    use testGetNewModifierReturnsExpectedIsPublicValueMyTraitOne {
        myTraitMethod as public;
    }
}
