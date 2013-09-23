<?php
trait testGetAllMethodsWithVisibilityChangedToProtected
{
    use testGetAllMethodsWithVisibilityChangedToProtectedUsedTraitOne {
        foo as protected;
    }
}

trait testGetAllMethodsWithVisibilityChangedToProtectedUsedTraitOne {
    public function foo() {}
}
