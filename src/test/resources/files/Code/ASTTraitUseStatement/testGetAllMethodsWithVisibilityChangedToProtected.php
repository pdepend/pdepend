<?php
class testGetAllMethodsWithVisibilityChangedToProtected
{
    use testGetAllMethodsWithVisibilityChangedToProtectedUsedTraitOne {
        foo as protected;
    }
}

trait testGetAllMethodsWithVisibilityChangedToProtectedUsedTraitOne {
    public function foo() {}
}
