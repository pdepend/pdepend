<?php
class A extends \PDepend\Source\AST\AbstractASTNode
{
    public function foo($bar = parent::FOO) {}
}
?>
