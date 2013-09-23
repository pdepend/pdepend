<?php
trait testGetAllMethodsWithVisibilityChangedToPrivate
{
    use testGetAllMethodsWithVisibilityChangedToPrivateUsedTraitOne {
        foo as private;
    }
}

trait testGetAllMethodsWithVisibilityChangedToPrivateUsedTraitOne
{
    public function foo() {}
}
