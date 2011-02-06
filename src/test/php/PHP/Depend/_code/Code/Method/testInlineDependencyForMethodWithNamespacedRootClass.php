<?php
namespace foo\bar\baz;

class testInlineDependencyForMethodWithNamespacedRootClass
{
    public function build()
    {
        /* @var $builder \ASTBuilder */
        $builder = create();
    }
}