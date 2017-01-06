<?php
class UnexpectedTokenTrait {
    public function foo() {
        throw new \LogicException('Not implemented ' . __TRAIT__);
    }
}
