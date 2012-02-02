<?php
class testGetAllMethodsWithAliasedMethodCollision {
    use testGetAllMethodsWithAliasedMethodCollisionUsedTraitOne,
        testGetAllMethodsWithAliasedMethodCollisionUsedTraitTwo {

        testGetAllMethodsWithAliasedMethodCollisionUsedTraitOne::bar as foo;
    }
}

trait testGetAllMethodsWithAliasedMethodCollisionUsedTraitOne {
    function bar() {}
}

trait testGetAllMethodsWithAliasedMethodCollisionUsedTraitTwo {
    function bar() {}
}
