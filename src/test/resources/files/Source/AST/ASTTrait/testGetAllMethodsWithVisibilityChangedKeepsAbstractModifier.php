<?php
trait testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
{
    use testGetAllMethodsWithVisibilityChangedKeepsAbstractModifierUsedTraitOne {
        foo as protected;
    }
}

trait testGetAllMethodsWithVisibilityChangedKeepsAbstractModifierUsedTraitOne {
    abstract public function foo();
}
