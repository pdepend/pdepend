<?php
class testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
{
    use testGetAllMethodsWithVisibilityChangedKeepsStaticModifierUsedTraitOne {
        foo as public;
    }
}

trait testGetAllMethodsWithVisibilityChangedKeepsStaticModifierUsedTraitOne
{
    protected static function foo()
    {

    }
}
