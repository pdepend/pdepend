<?php
trait testTokenizerReturnsExpectedConstantForTraitMagicConstant
{
    public function foo() {
        return __TRAIT__;
    }
}
