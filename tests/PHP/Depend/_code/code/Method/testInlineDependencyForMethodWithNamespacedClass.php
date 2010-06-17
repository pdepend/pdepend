<?php
namespace foo\bar\baz;

class testInlineDependencyForMethodWithNamespacedRootClass
{
    public function build()
    {
        /* @var $builder PDepend\Core\ASTBuilder */
        $builder = create();
    }
}