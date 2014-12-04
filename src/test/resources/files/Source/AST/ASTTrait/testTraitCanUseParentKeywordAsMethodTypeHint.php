<?php
trait testTraitCanUseParentKeywordAsMethodTypeHint
{
    public function baz(parent $foo)
    {
        echo get_class($foo), PHP_EOL;
    }
}

class Foo {
    use testTraitCanUseParentKeywordAsMethodTypeHint;
}

class Bar {
    use testTraitCanUseParentKeywordAsMethodTypeHint;
}

$foo = new Foo();
$bar = new Bar();

$foo->baz($foo);
