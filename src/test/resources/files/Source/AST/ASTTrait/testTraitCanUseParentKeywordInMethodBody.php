<?php
trait testTraitCanUseParentKeywordInMethodBody
{
    public function foo()
    {
        parent::foo();
    }
}
