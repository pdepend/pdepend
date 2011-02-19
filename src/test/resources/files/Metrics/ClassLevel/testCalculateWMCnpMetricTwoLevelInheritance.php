<?php
class A extends B {
    private $a;
    private function a() {}
    protected function b() {}
    public function c() {}
}

class B extends C {
    private function a2() {}
    public function b2() {}
    public function c2() {}
}

class C {
    public $c;
    private function a3() {}
    protected function b3() {}
    public function c3() {}
}
?>
