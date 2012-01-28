<?php
class testGetNewModifierReturnsExpectedIsProtectedValue
{
    use testGetNewModifierReturnsExpectedIsProtectedValueMyTraitOne {
        myTraitMethod as protected ;
    }
}
