<?php
class testFindChildrenOfTypeFindsASTNodeInMethodDeclarations
{
    public function foo($x, $y) {}
    public function bar($z) {}
    private function baz($baz) {}
}
