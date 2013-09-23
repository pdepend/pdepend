<?php
trait testGetAllMethodsWithAliasedMethodTwice
{
    use testGetAllMethodsWithAliasedMethodTwiceUsedTraitOne {
        baz as foo;
        baz as bar;
    }
}

trait testGetAllMethodsWithAliasedMethodTwiceUsedTraitOne
{
    function baz() {}
}
