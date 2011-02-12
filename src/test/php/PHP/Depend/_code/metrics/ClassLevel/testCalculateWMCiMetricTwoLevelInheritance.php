<?php
class A extends B {
    private $a;
    private function a() {}
    protected function b() {}
    public function c() {}
}
class B extends C {
    private function a2() {}
    protected function b2() {}
    public function c() {}
}
class C {
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
?>
