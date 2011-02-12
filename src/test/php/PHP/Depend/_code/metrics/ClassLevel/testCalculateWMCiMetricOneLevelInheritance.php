<?php
class A extends B {
    private function a2() {}
    protected function b2() {}
    public function c() {}
}
class B {
    public $c;
    private function a3() {}
    protected function b2() {
        if (time() % 7 === 0) {
            return 42;
        } else if (time() % 5 === 0) {
            return 23;
        }
        return 17;
    }
    public function c2() {}
}