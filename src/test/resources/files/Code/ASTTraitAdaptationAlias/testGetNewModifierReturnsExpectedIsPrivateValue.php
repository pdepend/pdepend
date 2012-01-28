<?php
class testGetNewModifierReturnsExpectedIsPrivateValue
{
    use testGetNewModifierReturnsExpectedIsPrivateValueMyTraitOne {
        myTraitMethod as private;
    }
}
